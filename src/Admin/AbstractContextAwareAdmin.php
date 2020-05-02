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

namespace App\Admin;


use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use App\Exporter\Source\CustomQuerySourceIterator;
use Doctrine\ORM\Query;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\OrderByToSelectWalker;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

/**
 * Class AbstractContextAwareAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-02-11
 */
abstract class AbstractContextAwareAdmin extends AbstractAdmin implements ContextAwareAdminInterface
{
    protected $customShowFields = ['serviceSystems', 'laboratories', 'services', 'publishedSolutions', 'solutions', 'serviceProviders',];

    protected $appContext = ContextAwareAdminInterface::APP_CONTEXT_BE;

    /**
     * @param string $appContext
     */
    public function setAppContext(string $appContext): void
    {
        $this->appContext = $appContext;
    }

    /**
     * @return string
     */
    public function getAppContext(): string
    {
        return $this->appContext;
    }

    protected function isFrontend(): bool
    {
        return $this->appContext === ContextAwareAdminInterface::APP_CONTEXT_FE;
    }

    /**
     * @return array
     */
    public function getCustomShowFields(): array
    {
        return $this->customShowFields;
    }

    public function getDataSourceIterator()
    {
        $datagrid = $this->getDatagrid();
        /** @noinspection NullPointerExceptionInspection */
        $datagrid->buildPager();

        $fields = [];

        foreach ($this->getExportFields() as $key => $field) {
            $label = $this->getTranslationLabel($field, 'export', 'label');
            $transLabel = $this->trans($label);

            // NEXT_MAJOR: Remove this hack, because all field labels will be translated with the major release
            // No translation key exists
            if ($transLabel === $label) {
                $fields[$key] = $field;
            } else {
                $fields[$transLabel] = $field;
            }
        }
        /** @noinspection NullPointerExceptionInspection */
        return $this->getCustomDataSourceIterator($datagrid, $fields);
    }

    /**
     * Create custom query source iterator for exporting collection variables
     *
     * @param DatagridInterface $datagrid
     * @param array $fields
     *
     * @return CustomQuerySourceIterator
     * @see \Sonata\DoctrineORMAdminBundle\Model\ModelManager::getDataSourceIterator
     */
    private function getCustomDataSourceIterator(DatagridInterface $datagrid, array $fields): CustomQuerySourceIterator
    {
        ini_set('max_execution_time', 0);
        $datagrid->buildPager();
        $query = $datagrid->getQuery();

        $query->select('DISTINCT '.current($query->getRootAliases()));
        $query->setFirstResult(null);
        $query->setMaxResults(null);

        if ($query instanceof ProxyQueryInterface) {
            $sortBy = $query->getSortBy();

            if (!empty($sortBy)) {
                $query->addOrderBy($sortBy, $query->getSortOrder());
                $query = $query->getQuery();
                $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, [OrderByToSelectWalker::class]);
            } else {
                $query = $query->getQuery();
            }
        }
        /** @var \Doctrine\ORM\Query $query */
        $container = $this->getConfigurationPool()->getContainer();
        $cacheDir = null;
        if (null !== $container) {
            $cacheDir = $container->getParameter('kernel.cache_dir');
        }

        return new CustomQuerySourceIterator(
            $query,
            $fields,
            $cacheDir,
            $this->getAppContext(),
            'd.m.Y H:i:s'
        );
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $preFields = ['id', 'createdAt', 'modifiedAt', 'createdBy'];
        $defaultFields = parent::getExportFields();
        $fields = [];
        foreach ($preFields as $field) {
            if (in_array($field, $defaultFields, false)) {
                $fields[] = $field;
            }
        }
        foreach ($defaultFields as $field) {
            if (!in_array($field, $fields, false)) {
                $fields[] = $field;
            }
        }
        $show = $this->getShow();
        if (null !== $show) {
            $showFields = array_keys($show->getElements());
            foreach ($showFields as $field) {
                if (!in_array($field, $fields, false)) {
                    $fields[] = $field;
                }
            }
        }
        $excludeFields = $this->getExportExcludeFields();
        if (!empty($excludeFields)) {
            $fields = array_diff($fields, $excludeFields);
        }
        return $fields;
    }

    /**
     * Add- custom query condition for full text data grid filter field
     * @param DatagridMapper $datagridMapper
     */
    protected function addFullTextDatagridFilter(DatagridMapper $datagridMapper)
    {
        $modelManager = $this->getModelManager();
        $entityClass = $this->getClass();
        $appContext = $this->getAppContext();
        $datagridMapper
            ->add('fullText', CallbackFilter::class, [
                'callback' => static function($queryBuilder, $alias, $field, $value) use ($modelManager, $entityClass, $appContext) {
                    if (!$value['value']) {
                        return false;
                    }
                    /** @var \Sonata\DoctrineORMAdminBundle\Model\ModelManager $modelManager */
                    $indexRepository = $modelManager->getEntityManager(SearchIndexWord::class)->getRepository(SearchIndexWord::class);
                    /** @var SearchIndexRepository $indexRepository */
                    $matchingRecordIds = $indexRepository->findMatchingIndexRecords($entityClass, $appContext, $value['value']);
                    if (null !== $matchingRecordIds) {

                        $queryBuilder
                            ->andWhere( $alias . ' IN(:matchingRecordIds)')
                            ->setParameter('matchingRecordIds', $matchingRecordIds);

                        return true;
                    }
                    return false;
                },
                'label' => 'app.common.full_text_search',
                'field_type' => SearchType::class,
                'show_filter' => true,
            ]);
    }

    /**
     * @return array
     */
    protected function getExportExcludeFields(): array
    {
        return ['hidden'];
    }

    public function getExportFormats()
    {
        return ['xlsx'];
    }
}
