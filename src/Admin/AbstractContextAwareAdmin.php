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

namespace App\Admin;


use App\Exporter\Source\CustomQuerySourceIterator;
use App\Model\ReferenceSettings;
use App\Service\ApplicationContextHandler;
use Doctrine\ORM\Query;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\OrderByToSelectWalker;

/**
 * Class AbstractContextAwareAdmin
 */
abstract class AbstractContextAwareAdmin extends AbstractAdmin implements ContextAwareAdminInterface
{

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

        $query->select('DISTINCT ' . current($query->getRootAliases()));
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
            ApplicationContextHandler::getDefaultAdminApplicationContext($this),
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

    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        if (!empty($parameters['_sort_order'])) {
            $parameters['_sort_order'] = $parameters['_sort_order'] === 'DESC' ? 'DESC' : 'ASC';
        }

        return $parameters;
    }

    /**
     * Returns the default reference settings for the reference lists in the detail views of other admins
     *
     * @param ApplicationContextHandler $applicationContextHandler The application context handler
     * @param string $editRouteName The edit route may be overridden in the field configuration
     * @return ReferenceSettings
     */
    public function getReferenceSettings(
        ApplicationContextHandler $applicationContextHandler,
        string $editRouteName = 'edit'): ReferenceSettings
    {
        $settings = new ReferenceSettings();
        $showRouteName = 'show';
        $settings->setAdmin($this);
        $isBackendMode = $applicationContextHandler->isBackend();
        $createShowLink = $this->hasRoute($showRouteName) && $this->hasAccess($showRouteName);
        $createEditLink = $isBackendMode && $this->hasRoute($editRouteName) && $this->hasAccess($editRouteName);
        $settings->setShow($createShowLink, $showRouteName);
        $settings->setEdit($createEditLink, $editRouteName);
        $settings->setListTitle($this->getLabel());
        return $settings;
    }

    /**
     * Returns the template for this admin used for creating the search index
     * @return string
     */
    public function getSearchIndexingTemplate(): string
    {
        return $this->getTemplateRegistry()->getTemplate('show');
    }
}
