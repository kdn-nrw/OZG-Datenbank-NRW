<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Search;

use App\Admin\EnableFullTextSearchAdminInterface;
use App\Entity\Base\BaseEntity;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\ImplementationProject;
use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use App\Entity\Service;
use App\Entity\ServiceSystem;
use App\Entity\Solution;
use App\EventSubscriber\SearchIndexEntityEvent;
use App\Service\ApplicationContextHandler;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

/**
 * Search indexer
 */
class Indexer extends AbstractSearchService
{

    /**
     * Limit for index entry age
     */
    public const INDEX_THRESHOLD = '-2 weeks';
    /**
     * @var Pool
     */
    private $adminPool;
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int|null
     */
    private $forceOnlyEntityId;

    /**
     * Enabled index contexts
     * @var array
     */
    private $indexContexts;

    /**
     * @param ManagerRegistry $registry
     * @param Environment $twigEnvironment
     * @param Pool $adminPool
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ManagerRegistry $registry,
        Environment $twigEnvironment,
        Pool $adminPool,
        EventDispatcherInterface $eventDispatcher
    )
    {
        parent::__construct($registry);
        $this->adminPool = $adminPool;
        $this->twigEnvironment = $twigEnvironment;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $contexts
     */
    public function setContexts(array $contexts): void
    {
        $this->indexContexts = $contexts;
    }

    private function getContexts(): array
    {
        if (null === $this->indexContexts) {
            $this->indexContexts = $this->applicationContextHandler->getAllContexts();
        }
        return $this->indexContexts;
    }

    /**
     * @param int|null $forceOnlyEntityId
     */
    public function setForceOnlyEntityId(?int $forceOnlyEntityId): void
    {
        $this->forceOnlyEntityId = $forceOnlyEntityId;
    }

    /**
     * Run the search indexing process
     *
     * @param int $limit
     * @param string|null $onlyEntityClass
     * @return int Number of update records
     */
    public function run(int $limit, ?string $onlyEntityClass): int
    {
        $count = 0;
        $maxResults = $limit;
        $adminClasses = $this->getAdminClasses();
        foreach ($adminClasses as $entityClass => $classAdmins) {
            if ((!$onlyEntityClass || $onlyEntityClass === $entityClass) &&
                is_subclass_of($entityClass, BaseEntity::class)) {
                $count += $this->updateEntityClassIndex($entityClass, $classAdmins, $maxResults);
                $maxResults = $limit - $count;
                if ($maxResults < 1) {
                    break;
                }
            }
        }
        return $count;
    }

    /**
     * Returns the entity admin class mapping
     *
     * @return array
     */
    private function getAdminClasses(): array
    {
        // Prepend the classes that are visible in the frontend
        $prependClasses = [
            Solution::class,
            ImplementationProject::class,
            Service::class,
            ServiceSystem::class,
        ];
        shuffle($prependClasses);
        $tmpAdminClasses = $this->adminPool->getAdminClasses();
        $keys = array_keys($tmpAdminClasses);
        shuffle($keys);
        $keys = array_unique(array_merge($prependClasses, $keys));
        $adminClasses = [];
        foreach ($keys as $entityClass) {
            $adminClasses[$entityClass] = $tmpAdminClasses[$entityClass];
        }
        return $adminClasses;
    }

    /**
     * Update search index for all entities with the given class
     *
     * @param string $entityClass
     * @param array $classAdmins
     * @param int $limit
     * @return int Count of indexed entities
     */
    private function updateEntityClassIndex(string $entityClass, array $classAdmins, int $limit): int
    {
        $count = 0;
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->registry->getManager();
        // Save memory
        $em->getConnection()->getConfiguration()->setSQLLogger();
        foreach ($classAdmins as $adminClass) {
            $admin = $this->adminPool->getAdminByAdminCode($adminClass);
            if ($admin instanceof EnableFullTextSearchAdminInterface) {
                if ($this->forceOnlyEntityId) {
                    $entity = $admin->getModelManager()->find($entityClass, $this->forceOnlyEntityId);
                    if (null !== $entity) {
                        $count += $this->updateEntityList([$entity], $admin, true);
                    }
                } else {
                    $maxResultsPerCycle = 50;
                    $firstResultOffset = 0;
                    while ($firstResultOffset < $limit) {
                        $query = $admin->createQuery();
                        if (method_exists($entityClass, 'getModifiedAt')) {
                            $query->setSortBy([], ['fieldName' => 'modifiedAt']);
                            $query->setSortOrder('DESC');
                        }
                        $query->setFirstResult($firstResultOffset);
                        $query->setMaxResults($maxResultsPerCycle);
                        $result = $query->execute();
                        $count += $this->updateEntityList($result, $admin, false);
                        $firstResultOffset += $maxResultsPerCycle;
                        $em->flush();
                        // Clear all entities before continuing with the next admin
                        $em->clear();
                        // Mark as ended when fewer rows are returned than allowed
                        if ($count > $limit || count($result) < $maxResultsPerCycle) {
                            break;
                        }
                    }
                }
            }
        }
        $em->flush();
        $em->clear();
        return $count;
    }

    /**
     * Update the search index for the given entity list
     * @param array|BaseEntityInterface[]|mixed $entityList
     * @param AdminInterface|EnableFullTextSearchAdminInterface $admin
     * @param bool $forceUpdate
     * @return int
     */
    private function updateEntityList(
        $entityList,
        AdminInterface $admin,
        bool $forceUpdate
    ): int
    {
        $context = ApplicationContextHandler::getDefaultAdminApplicationContext($admin);
        if (!in_array($context, $this->getContexts(), false)) {
            return 0;
        }
        $entityClass = $admin->getClass();
        $em = $this->registry->getManager();
        $baseTemplate = '@SonataAdmin/ajax_layout.html.twig';
        $fields = $admin->getShow();
        $template = $admin->getSearchIndexingTemplate();
        $count = 0;

        foreach ($entityList as $entity) {
            if ($forceUpdate || $this->itemIndexNeedsUpdate($entityClass, $context, $entity)) {
                $parameters = [
                    'action' => 'show',
                    'object' => $entity,
                    'elements' => $fields,
                    'admin' => $admin,
                    'base_template' => $baseTemplate,
                    'admin_pool' => $this->adminPool,
                ];
                try {
                    $content = $this->twigEnvironment->render($template, $parameters);
                    $this->updateEntityIndex($admin, $entity, $context, $content);
                } catch (Exception $e) {
                    echo 'Entity indexing failed: ' . $entityClass . ':' . $entity->getId() . ' => '
                        . $e->getMessage() . ' [' . $e->getCode() . ']'
                        . $e->getFile() . '::' . $e->getLine() . "\n";
                }
                ++$count;
                if ($count % 10 === 0) {
                    $em->flush();
                }
            }
            unset($entity);
        }
        return $count;
    }

    /**
     * @param AdminInterface $admin The admin instance
     * @param BaseEntity $entity The entity
     * @param string $context The view context (frontend or backend)
     * @param string $content The rendered view content
     */
    private function updateEntityIndex(AdminInterface $admin, BaseEntity $entity, string $context, string $content): void
    {
        $em = $this->registry->getManager();
        $indexRepository = $this->getIndexRepository();
        /** @var SearchIndexRepository $indexRepository */
        $fullTextSearchWords = TextProcessor::createWordListForText($content);
        /** @var SearchIndexWord[] $mapEntries */
        $mapEntries = [];
        $entityClass = $admin->getClass();
        $indexEntries = $indexRepository->findBy([
            'module' => $entityClass,
            'recordId' => $entity->getId(),
            'context' => $context,
        ]);
        /** @var SearchIndexWord[] $indexEntries */
        foreach ($indexEntries as $indexEntry) {
            $indexEntry->setHidden(true);
            $mapEntries[$indexEntry->getBaseword()] = $indexEntry;
        }
        try {
            $event = new SearchIndexEntityEvent($admin, $entity, $fullTextSearchWords);
            $this->eventDispatcher->dispatch($event);
            $processedSearchWords = $event->getFullTextSearchWords();
        } catch (Exception $e) {
            $processedSearchWords = $fullTextSearchWords;
            echo 'Entity SearchIndexEntityEvent failed: ' . $entityClass . ':' . $entity->getId() . ' => '
                . $e->getMessage() . ' [' . $e->getCode() . ']'
                . $e->getFile() . '::' . $e->getLine() . "\n";
        }
        foreach ($processedSearchWords as $word => $wordMeta) {
            $occurrence = (int)$wordMeta['count'];
            $isGenerated = (bool)$wordMeta['is_generated'];
            $searchWord = (string)$word;
            if (isset($mapEntries[$searchWord])) {
                $indexEntry = $mapEntries[$searchWord];
                $indexEntry->setHidden(false);
                $indexEntry->setOccurrence($occurrence);
                $indexEntry->setIsGenerated($isGenerated);
                $indexEntry->setModifiedAt(new DateTime());
            } else {
                $indexEntry = new SearchIndexWord();
                $indexEntry->setBaseword($searchWord);
                $indexEntry->setRecordId($entity->getId());
                $indexEntry->setModule($entityClass);
                $indexEntry->setContext($context);
                $isStopWord = mb_strlen($searchWord) < 3;
                $phonetic = PhoneticUtility::getPhoneticRepresentationForWord($searchWord);
                $indexEntry->setIsStopword($isStopWord);
                $indexEntry->setIsGenerated($isGenerated);
                $indexEntry->setMetaphone($phonetic);
                $indexEntry->setHidden(false);
                $indexEntry->setOccurrence($occurrence);
                $em->persist($indexEntry);
            }
        }
        /** @var SearchIndexWord[] $indexEntries */
        foreach ($indexEntries as $indexEntry) {
            if ($indexEntry->isHidden()) {
                $em->remove($indexEntry);
            }
        }
    }

    /**
     * Check if given entity needs a search index update
     *
     * @param string $entityClass
     * @param string $context
     * @param BaseEntity $entity
     * @return bool
     */
    private function itemIndexNeedsUpdate(string $entityClass, string $context, BaseEntity $entity): bool
    {
        $indexRepository = $this->getIndexRepository();
        $lastIndexTime = $indexRepository->getRecordLastIndexTime($entityClass, $context, $entity->getId());
        if (null !== $lastIndexTime) {
            try {
                $lastIndexDateTime = new DateTime($lastIndexTime, new DateTimeZone('UTC'));
                $lastIndexTimestamp = $lastIndexDateTime->getTimestamp();
            } catch (Exception $e) {
                $lastIndexTimestamp = strtotime('-1 day');
            }
            // Prevent indexing of same record more than once per hour
            if ($lastIndexTimestamp > time() - 3600) {
                return false;
            }
            $modifiedAt = $entity->getModifiedAt();
            $minReIndexTimestamp = strtotime(self::INDEX_THRESHOLD);
            $lastModifiedTimestamp = null !== $modifiedAt ? $modifiedAt->getTimestamp() : 0;
            $itemNeedsReindex = $lastIndexTimestamp < $minReIndexTimestamp || ($lastModifiedTimestamp > $lastIndexTimestamp);
            //echo "Last index time of record ".$entity->getId()." was ".date('Y-m-d H:i:s', $lastIndexTimestamp)."; Reindex? ".($itemNeedsReindex ? 'Y' : 'N')." \n";
            /*if ($itemNeedsReindex) {
                echo 'Indexing ' . $entityClass . ':' . $entity->getId()
                    . '; force index after: ' . date('d.m.Y H:i:s', $minReIndexTimestamp)
                    . '; last index: ' . date('d.m.Y H:i:s', $lastIndexTimestamp)
                    . '; last modified: ' . date('d.m.Y H:i:s', $lastModifiedTimestamp)
                    . "\n";
            }*/
        } else {
            $itemNeedsReindex = true;
            /*echo 'Indexing ' . $entityClass . ':' . $entity->getId()
                . '; last index: never'
                . "\n";*/
        }
        return $itemNeedsReindex;
    }
}