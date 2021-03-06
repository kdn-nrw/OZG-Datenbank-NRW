<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Admin\Extension;

use App\Admin\EnableFullTextSearchAdminInterface;
use App\Datagrid\FulltextSearchDatagridInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Search\Finder;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

/**
 * Admin extension for admin search settings
 */
class SearchExtension extends AbstractAdminExtension
{
    private const FILTER_KEY = 'fullText';

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * SearchExtension constructor.
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }


    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $admin = $datagridMapper->getAdmin();
        if ($admin instanceof EnableFullTextSearchAdminInterface) {
            $keys = $datagridMapper->keys();
            $this->addFullTextDatagridFilter($datagridMapper);
            $datagridMapper->reorder([self::FILTER_KEY] + $keys);
        }
    }

    /**
     * Add- custom query condition for full text data grid filter field
     * @param DatagridMapper $datagridMapper
     */
    private function addFullTextDatagridFilter(DatagridMapper $datagridMapper): void
    {
        $admin = $datagridMapper->getAdmin();
        $datagridMapper
            ->add(self::FILTER_KEY, CallbackFilter::class, [
                'callback' => function (ProxyQueryInterface $queryBuilder, $alias, $field, $value) use ($admin) {
                    if (!$value['value']) {
                        return false;
                    }
                    $entityClass = $admin->getClass();
                    $dataGrid = $admin->getDatagrid();
                    /** @var QueryBuilder $qb */
                    $qb = $queryBuilder->getQueryBuilder();
                    $reflect = new \ReflectionClass($entityClass);
                    $props = $reflect->getProperties();
                    $orConditions = [];

                    $matchingRecordIds = $this->finder->findMatchingRecordIds($entityClass, (string) $value['value']);
                    $extraSearchFields = null;
                    $resultCount = 0;
                    if (null !== $matchingRecordIds) {
                        $matchingIdCount = count($matchingRecordIds);
                        $resultCount = $matchingIdCount;
                        if ($matchingIdCount > 0 && $dataGrid instanceof FulltextSearchDatagridInterface) {
                            $filterParameters = $admin->getFilterParameters();
                            $maxPerPage = FulltextSearchDatagridInterface::DEFAULT_MAX_RESULTS_PER_PAGE;
                            if (isset($filterParameters['_per_page'])) {
                                if (isset($filterParameters['_per_page']['value'])) {
                                    $maxPerPage = $filterParameters['_per_page']['value'];
                                } else {
                                    $maxPerPage = $filterParameters['_per_page'];
                                }
                            }
                            // Only load the entries for the current page; used for custom sorting in DataGrid
                            if ($matchingIdCount > $maxPerPage) {
                                $page = $dataGrid->getPager()->getPage();
                                $startOffset = ($page - 1) * $maxPerPage;
                                // Load more records oin case other filters are active, so the $maxPerPage rows will be loaded
                                $endOffset = min($startOffset + $maxPerPage, $matchingIdCount - 1);
                                $loadIdList = [];
                                for ($o = $startOffset; $o < $endOffset; $o++) {
                                    $loadIdList[] = $matchingRecordIds[$o];
                                }
                                $activeFilterRecordIds = $loadIdList;
                            } else {
                                $activeFilterRecordIds = $matchingRecordIds;
                            }
                            $dataGrid->setCustomOrderRecordIdList($activeFilterRecordIds);
                        } else {
                            $activeFilterRecordIds = $matchingRecordIds;
                        }
                        $orConditions[] = $alias . ' IN(:matchingRecordIds)';
                        $queryBuilder->setParameter('matchingRecordIds', $activeFilterRecordIds);
                        // If indexer has record, only search the main string fields in addition to the
                        // search index to make the search faster
                        $extraSearchFields = ['name', 'description'];
                    }
                    $addSearchFields = [];
                    // Name is already searched for in full text search
                    if (is_a($entityClass, NamedEntityInterface::class, true)) {
                        $addSearchFields['name'] = false;
                    }
                    $words = array_filter(array_map('trim', explode(' ', $value['value'])));
                    if ($resultCount < 6) {
                        foreach ($props as $refProperty) {
                            if ((false !== $docComment = $refProperty->getDocComment())
                                && preg_match('/@var\s+([^\s]+)/', $docComment, $matches)) {
                                $field = $refProperty->getName();
                                $type = $matches[1];
                                if (($type === 'string' || $type === 'string|null')
                                    && !isset($addSearchFields[$field])
                                    && (null === $extraSearchFields || in_array($field, $extraSearchFields, false))) {
                                    $addSearchFields[$field] = 'both';
                                }
                            }
                        }
                    }
                    if (!empty($addSearchFields)) {
                        foreach ($addSearchFields as $addSearchField => $placeholderPosition) {
                            if ($placeholderPosition !== false) {
                                foreach ($words as $word) {
                                    $literal = (in_array($placeholderPosition, ['before', 'both']) ? '%' : '')
                                        . strtolower($word)
                                        . (in_array($placeholderPosition, ['after', 'both']) ? '%' : '');
                                    $orConditions[] = $qb->expr()->like(
                                        'LOWER('.$alias . '.' . $addSearchField.')',
                                        $queryBuilder->expr()->literal($literal)
                                    );
                                }
                            }
                        }
                    }
                    if (!empty($orConditions)) {
                        $qb
                            ->andWhere($qb->expr()->orX()->addMultiple($orConditions));

                        return true;
                    }
                    return false;
                },
                'label' => 'app.common.full_text_search',
                'field_type' => SearchType::class,
                'show_filter' => true,
                'global_search' => true,
            ]);
    }
}
