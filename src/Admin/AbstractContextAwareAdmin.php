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


use App\Admin\Base\AdminTranslatorStrategyTrait;
use App\Datagrid\CustomDatagrid;
use App\Entity\Base\SluggableInterface;
use App\Exporter\Source\CustomQuerySourceIterator;
use App\Form\Filter\GroupedSessionFilterPersister;
use App\Model\ExportSettings;
use App\Model\ReferenceSettings;
use App\Service\ApplicationContextHandler;
use App\Service\InjectAdminHelperTrait;
use App\Service\InjectAdminManagerTrait;
use App\Sonata\AdminBundle\Admin\AbstractAdmin;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Doctrine\ORM\Query;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
/**
 * Class AbstractContextAwareAdmin
 */
abstract class AbstractContextAwareAdmin extends AbstractAdmin implements ContextAwareAdminInterface, CustomExportAdminInterface
{
    use InjectAdminManagerTrait;
    use InjectAdminHelperTrait;
    use AdminTranslatorStrategyTrait;

    public function getDataSourceIterator(): \Iterator
    {
        $datagrid = $this->getDatagrid();
        $datagrid->buildPager();

        $exportSettings = $this->getProcessedExportSettings();
        return $this->getCustomDataSourceIterator($datagrid, $exportSettings);
    }

    /**
     * Create custom query source iterator for exporting collection variables
     *
     * @param DatagridInterface $datagrid
     * @param ExportSettings $exportSettings
     *
     * @return CustomQuerySourceIterator
     * @see \Sonata\DoctrineORMAdminBundle\Model\ModelManager::getDataSourceIterator
     */
    private function getCustomDataSourceIterator(DatagridInterface $datagrid, ExportSettings $exportSettings): CustomQuerySourceIterator
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
        $exportSettings->setContext(ApplicationContextHandler::getDefaultAdminApplicationContext($this));
        return new CustomQuerySourceIterator(
            $this,
            $query,
            $this->adminManager->getCache(),
            $exportSettings
        );
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        return new ExportSettings();
    }

    /**
     * Returns the available export formats
     *
     * @return array|string[]
     */
    public function getExportFormats(): array
    {
        return $this->getExportSettings()->getFormats();
    }

    /**
     * Returns the export settings with the processed fields
     *
     * @return ExportSettings
     */
    public function getProcessedExportSettings(): ExportSettings
    {
        $fields = [];
        $exportSettings = $this->getExportSettings();

        $domain = $this->getTranslationDomain();
        foreach ($this->getExportFields() as $key => $field) {
            $transLabel = $exportSettings->getCustomLabel($field);
            if (!$transLabel) {
                $label = $this->getTranslationLabel($field, 'export', 'label');
                $transLabel = $this->getTranslator()->trans($label, [], $domain);
            } else {
                $label = $field;
            }

            // NEXT_MAJOR: Remove this hack, because all field labels will be translated with the major release
            // No translation key exists
            if ($transLabel === $label) {
                $fields[$key] = $field;
            } else {
                $fields[$transLabel] = $field;
            }
        }
        $exportSettings->setProcessedPropertyMap($fields);
        return $exportSettings;
    }

    /**
     * Configures a list of default sort values.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {

        if (!empty($sortValues['_sort_order'])) {
            $sortValues['_sort_order'] = $sortValues['_sort_order'] === 'DESC' ? 'DESC' : 'ASC';
        }
    }

    /**
     * Adds the data grid filter base on the property configuration
     * @param DatagridMapper $filter
     * @param string $property
     * @param array $filterOptions
     */
    final protected function addDefaultDatagridFilter(DatagridMapper $filter, string $property, array $filterOptions = []): void
    {
        $this->adminHelper->addDefaultDataGridFilter($this->getClass(), $filter, $property, $filterOptions);
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
        // Don't create show link if admin is only visible in frontend
        $createShowLink = ($isBackendMode
                || ApplicationContextHandler::getDefaultAdminApplicationContext($this) === ApplicationContextHandler::APP_CONTEXT_FE)
            && $this->hasRoute($showRouteName) && $this->hasAccess($showRouteName);
        $createEditLink = $isBackendMode && $this->hasRoute($editRouteName) && $this->hasAccess($editRouteName);
        $enableSlug = false;
        if ($createShowLink && !$isBackendMode) {
            $class = new \ReflectionClass($this->getClass());
            $enableSlug = $class->implementsInterface(SluggableInterface::class);
        }
        $settings->setShow($createShowLink, $showRouteName, $enableSlug);
        $settings->setEdit($createEditLink, $editRouteName);
        $settings->setListTitle($this->getLabel());
        $settings->setLabelPrefix(PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($this->getClass()));
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

    /**
     * @param string|null $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     * @deprecated use $this->getTranslator()->trans
     */
    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        $domain = $domain ?: $this->getTranslationDomain();
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }
}
