<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Search;

use App\Admin\ContextAwareAdminInterface;
use App\Entity\Base\BaseEntity;
use App\Entity\ImplementationProject;
use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use App\Entity\Service;
use App\Entity\ServiceSystem;
use App\Entity\Solution;
use App\EventSubscriber\SearchIndexEntityEvent;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Search indexer
 */
class Indexer
{
    private const MAX_WORD_LENGTH = 128;

    /**
     * Limit for index entry age
     */
    public const INDEX_THRESHOLD = '-2 weeks';

    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|ManagerRegistry
     */
    private $registry;
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
    private $indexContexts = [
        ContextAwareAdminInterface::APP_CONTEXT_FE,
        ContextAwareAdminInterface::APP_CONTEXT_BE,
    ];

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
        $this->registry = $registry;
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function run(int $limit, ?string $onlyEntityClass): int
    {
        $count = 0;
        $adminClasses = $this->getAdminClasses();
        foreach ($adminClasses as $entityClass => $classAdmins) {
            if ((!$onlyEntityClass || $onlyEntityClass === $entityClass) &&
                is_subclass_of($entityClass, BaseEntity::class)) {
                $count += $this->updateEntityClassIndex($entityClass, $classAdmins, $limit);
                if ($count > $limit) {
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function updateEntityClassIndex(string $entityClass, array $classAdmins, int $limit): int
    {
        $count = 0;
        $em = $this->registry->getManager();
        foreach ($classAdmins as $adminClass) {
            $admin = $this->adminPool->getAdminByAdminCode($adminClass);
            if ($admin instanceof ContextAwareAdminInterface) {
                $context = $admin->getAppContext();
                if (!in_array($context, $this->indexContexts, false)) {
                    continue;
                }
                $baseTemplate = $admin->getTemplate('ajax');
                $fields = $admin->getShow();
                $template = $admin->getTemplate('show');
                if ($this->forceOnlyEntityId) {
                    $forceUpdate = true;
                    $entity = $admin->getModelManager()->find($entityClass, $this->forceOnlyEntityId);
                    $result = [];
                    if (null !== $entity) {
                        $result[] = $entity;
                    }
                } else {
                    $forceUpdate = false;
                    $query = $admin->createQuery('list');
                    if (method_exists($entityClass, 'getModifiedAt')) {
                        $query->setSortBy([], ['fieldName' => 'modifiedAt']);
                        $query->setSortOrder('DESC');
                    }
                    $result = $query->execute();
                }
                foreach ($result as $entity) {
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
                        } catch (\Exception $e) {
                            echo 'Entity indexing failed: ' . $entityClass . ':' . $entity->getId() . ' => '
                                . $e->getMessage() . ' ['.$e->getCode().']'
                                . $e->getFile() . '::'.$e->getLine()."\n";
                        }
                        ++$count;
                        if ($count % 10 === 0) {
                            $em->flush();
                        }
                        if ($count > $limit) {
                            break 2;
                        }
                    }
                }
                $em->flush();
                $em->clear();
            }
        }
        $em->flush();
        $em->clear();
        return $count;
    }

    /**
     * @param AbstractAdmin|ContextAwareAdminInterface $admin The admin instance
     * @param BaseEntity $entity The entity
     * @param string $context The view context (frontend or backend)
     * @param string $content The rendered view content
     */
    private function updateEntityIndex(AbstractAdmin $admin, BaseEntity $entity, string $context, string $content): void
    {
        $em = $this->registry->getManager();
        $indexRepository = $em->getRepository(SearchIndexWord::class);
        /** @var SearchIndexRepository $indexRepository */
        $fullTextSearchWords = $this->filterContent($content);
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
        } catch (\Exception $e) {
            $processedSearchWords = $fullTextSearchWords;
            echo 'Entity SearchIndexEntityEvent failed: ' . $entityClass . ':' . $entity->getId() . ' => '
                . $e->getMessage() . ' ['.$e->getCode().']'
                . $e->getFile() . '::'.$e->getLine()."\n";
        }
        foreach ($processedSearchWords as $word => $occurrence) {
            $searchWord = (string)$word;
            if (isset($mapEntries[$searchWord])) {
                $indexEntry = $mapEntries[$searchWord];
                $indexEntry->setHidden(false);
                $indexEntry->setOccurrence($occurrence);
                $indexEntry->setModifiedAt(new DateTime());
            } else {
                $indexEntry = new SearchIndexWord();
                $indexEntry->setBaseword($searchWord);
                $indexEntry->setRecordId($entity->getId());
                $indexEntry->setModule($entityClass);
                $indexEntry->setContext($context);
                $isStopWord = mb_strlen($searchWord) < 3;
                $metaphone = metaphone($searchWord);
                if ($metaphone === '') {
                    $metaphone = 0;
                } else {
                    $metaphone = hexdec(mb_substr(md5($metaphone), 0, 7));
                }
                $indexEntry->setIsStopword($isStopWord);
                $indexEntry->setMetaphone($metaphone);
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
        $em = $this->registry->getManager();
        $indexRepository = $em->getRepository(SearchIndexWord::class);
        /** @var SearchIndexRepository $indexRepository */
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

    /**
     * Filter the given HTML content and find the relevant search words in the content
     * @param string $content HTML content
     * @return array
     */
    private function filterContent($content): array
    {
        $fullTextSearchWords = [];
        $filteredContent = preg_replace('/<th>[\wäöüÜÖÄß\s@:.\/\-]+<\/th>/u', '', $content);
        $filteredContent = preg_replace('/<!-- [\wäöüÜÖÄß\s@:.\/\-]+-->/u', '', $filteredContent);
        //$filteredContent = str_replace(['>', PHP_EOL, '?', '*'], ['> ', ' ', ' ', ' '], $filteredContent);
        $filteredContent = str_replace(['>', PHP_EOL], ['> ', ' '], $filteredContent);
        $indexText = trim(strip_tags($filteredContent));
        if ($indexText !== '') {
            $indexText = $this->filterUrls($indexText);
            $patternText = '/[^0-9a-zA-ZäöüÜÖÄß]+/u';
            $searchTerm = preg_replace($patternText, ' ', $indexText);
            $searchTerm = mb_strtolower(str_replace("\n", ' ', $searchTerm));
            $searchWordList = explode(' ', $searchTerm);
            // Add additional words with . and - (so we can search both the word parts and the whole words)
            $patternTextAdd = "/[^0-9a-zA-ZäöüÜÖÄß\.-]+/u";
            $extendedSearchTerm = preg_replace($patternTextAdd, ' ', $indexText);
            $stAddChars = mb_strtolower(str_replace("\n", ' ', $extendedSearchTerm));
            $extendedSearchWords = explode(' ', $stAddChars);
            foreach ($extendedSearchWords as $word) {
                $cleanWord = trim($word, '.-');
                if (!in_array($cleanWord, $searchWordList, false)) {
                    $searchWordList[] = $cleanWord;
                }
            }
            $umlauts = array(
                'ö' => array('o', 'oe'),
                'ü' => array('u', 'ue'),
                'ä' => array('a', 'ae'),
                'ß' => array('ss',),
            );
            foreach ($searchWordList as $searchWord) {
                if (mb_strlen($searchWord) > 2) {
                    $dbWord = $searchWord;
                    $this->addSearchWord($dbWord, $fullTextSearchWords);
                    foreach ($umlauts as $umlaut => $altVals) {
                        if (mb_strpos($dbWord, $umlaut) !== false) {
                            foreach ($altVals as $altVal) {
                                $altWord = str_replace($umlaut, $altVal, $dbWord);
                                $this->addSearchWord($altWord, $fullTextSearchWords);
                            }
                        }
                    }
                }
            }
        }
        return $fullTextSearchWords;
    }

    /**
     * Filter urls in content (remove url parameters)
     *
     * @param string $indexText
     * @return string
     */
    private function filterUrls(string $indexText)
    {
        $pattern = '@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@';
        preg_match_all($pattern, $indexText, $urlMatches);
        foreach ($urlMatches[0] as $url) {
            $urlParts = parse_url($url);
            if ($urlParts !== false && !empty($urlParts['host'])) {
                $urlPath = !empty($urlParts['path']) ? str_replace('.html', '', $urlParts['path']) : '';
                $indexText = str_replace($url, $urlParts['host'] . ' ' . $urlPath, $indexText);
            }
        }
        return $indexText;
    }

    /**
     * Add word to search word list
     *
     * @param string $word
     * @param array $fullTextSearchWords
     */
    private function addSearchWord(string $word, array &$fullTextSearchWords): void
    {
        // fix long words without spaces
        if (mb_strlen($word) > self::MAX_WORD_LENGTH) {
            $length = (int) floor(self::MAX_WORD_LENGTH / 2);
            $this->addSearchWord(mb_substr($word, 0, $length), $fullTextSearchWords);
            $this->addSearchWord(mb_substr($word, $length - 1), $fullTextSearchWords);
        } else {
            if (!isset($fullTextSearchWords[$word])) {
                $fullTextSearchWords[$word] = 0;
            }
            ++$fullTextSearchWords[$word];
        }
    }
}