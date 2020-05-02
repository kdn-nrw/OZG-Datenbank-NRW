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
use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Admin\Pool;
use Twig\Environment;

/**
 * Search indexer
 */
class Indexer
{
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
     * @param ManagerRegistry $registry
     * @param Environment $twigEnvironment
     * @param Pool $adminPool
     */
    public function __construct(ManagerRegistry $registry, Environment $twigEnvironment, Pool $adminPool)
    {
        $this->registry = $registry;
        $this->adminPool = $adminPool;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function run(int $limit): void
    {
        $adminClasses = $this->adminPool->getAdminClasses();
        $count = 0;
        foreach ($adminClasses as $entityClass => $classAdmins) {
            $count += $this->createEntityClassIndex($entityClass, $classAdmins, $limit);
            if ($count > $limit) {
                break;
            }
        }
    }

    private function createEntityClassIndex(string $entityClass, array $classAdmins, int $limit)
    {
        $count = 0;
        foreach ($classAdmins as $adminClass) {
            $admin = $this->adminPool->getAdminByAdminCode($adminClass);
            if ($admin instanceof ContextAwareAdminInterface) {
                $baseTemplate = $admin->getTemplate('ajax');
                $fields = $admin->getShow();
                $template = $admin->getTemplate('show');
                $query = $admin->createQuery('list');
                $context = $admin->getAppContext();
                $result = $query->execute();
                foreach ($result as $entity) {
                    if ($this->itemIndexNeedsUpdate($entityClass, $context, $entity)) {
                        $parameters = [
                            'action' => 'show',
                            'object' => $entity,
                            'elements' => $fields,
                            'admin' => $admin,
                            'base_template' => $baseTemplate,
                            'admin_pool' => $this->adminPool,
                        ];
                        $content = $this->twigEnvironment->render($template, $parameters);
                        $this->updateEntityIndex($entityClass, $entity, $context, $content);
                        ++$count;
                        if ($count > $limit) {
                            break 2;
                        }
                    }
                }
            }
        }
        return $count;
    }

    private function updateEntityIndex(string $entityClass, BaseEntity $entity, string $context, string $content)
    {
        $em = $this->registry->getManager();
        $indexRepository = $em->getRepository(SearchIndexWord::class);
        /** @var SearchIndexRepository $indexRepository */
        $fullTextSearchWords = $this->filterContent($content);
        /** @var SearchIndexWord[] $mapEntries */
        $mapEntries = [];
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
        foreach ($fullTextSearchWords as $word => $occurrence) {
            $searchWord = (string) $word;
            if (isset($mapEntries[$searchWord])) {
                $indexEntry = $mapEntries[$searchWord];
                $indexEntry->setHidden(false);
                $indexEntry->setOccurrence($occurrence);
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
        $em->flush();
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
        $modifiedAt = $entity->getModifiedAt();
        $em = $this->registry->getManager();
        $indexRepository = $em->getRepository(SearchIndexWord::class);
        /** @var SearchIndexRepository $indexRepository */
        $lastIndexTime = $indexRepository->getRecordLastIndexTime($entityClass, $context, $entity->getId());
        if (null !== $lastIndexTime) {
            $lastIndexTimestamp = strtotime($lastIndexTime);
            $minReIndexTimestamp = strtotime(self::INDEX_THRESHOLD);
            return $lastIndexTimestamp < $minReIndexTimestamp
                || (null !== $modifiedAt && $modifiedAt->getTimestamp() > $lastIndexTimestamp);
        }
        return true;
    }

    private function filterContent($content) {
        $fullTextSearchWords = [];
        $filteredContent = preg_replace('/<th>[\wäöüÜÖÄß\s@:.\/\-]+<\/th>/u', '', $content);
        $filteredContent = preg_replace('/<!-- [\wäöüÜÖÄß\s@:.\/\-]+-->/u', '', $filteredContent);
        $filteredContent = str_replace(['>', PHP_EOL], ['> ', ' '], $filteredContent);
        $indexText = trim(strip_tags($filteredContent));
        if ($indexText !== '') {
            $patternText = '/[^0-9a-zA-ZäöüÜÖÄß]+/u';
            $searchTerm =  preg_replace($patternText, ' ', $indexText);
            $searchTerm = mb_strtolower(str_replace("\n", ' ', $searchTerm));
            $searchWordList = explode(' ', $searchTerm);
            // Add additional words with . and - (so we can search both the word parts and the whole words)
            $patternTextAdd = "/[^0-9a-zA-ZäöüÜÖÄß\.-]+/u";
            $extendedSearchTerm = preg_replace($patternTextAdd, ' ', $indexText);
            $stAddChars = mb_strtolower(str_replace("\n", ' ', $extendedSearchTerm));
            $extendedSearchWords = explode(' ', $stAddChars);
            foreach($extendedSearchWords as $word){
                $cleanWord = trim($word, '.-');
                if(!in_array($cleanWord, $searchWordList, false)){
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
                    if(!isset($fullTextSearchWords[$dbWord])){
                        $fullTextSearchWords[$dbWord] = 0;
                    }
                    ++$fullTextSearchWords[$dbWord];
                    foreach ($umlauts as $umlaut => $altVals) {
                        if (mb_strpos($dbWord, $umlaut) !== false) {
                            foreach ($altVals as $altVal) {
                                $altWord = str_replace($umlaut, $altVal, $dbWord);
                                if(!isset($fullTextSearchWords[$altWord])){
                                    $fullTextSearchWords[$altWord] = 0;
                                }
                                ++$fullTextSearchWords[$altWord];
                            }
                        }
                    }
                }
            }
        }
        return $fullTextSearchWords;
    }
}