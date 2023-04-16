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

namespace App\Admin\Frontend;

use App\Admin\AbstractContextAwareAdmin;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\SluggableInterface;
use App\Model\ExportSettings;
use RuntimeException;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as RoutingUrlGeneratorInterface;

/**
 * Class AbstractFrontendAdmin
 */
abstract class AbstractFrontendAdmin extends AbstractContextAwareAdmin implements ContextFrontendAdminInterface
{
    protected $adminBaseRouteName;
    protected $adminBaseRoutePattern;
    /**
     * Label for the default show group header
     * @var string
     */
    protected $defaultShowGroupLabel = 'object_name';

    /**
     * Action list for the search result.
     *
     * @var string[]
     */
    protected $searchResultActions = ['show'];

    /**
     * @return string|null
     */
    protected function getTranslatorNamingPrefix(): ?string
    {
        if (null === $this->translatorNamingPrefix) {
            $this->translatorNamingPrefix = str_replace('\\Frontend', '', get_class($this));
        }
        return $this->translatorNamingPrefix;
    }

    /**
     * Initialized the routes and templates for this admin
     */
    public function initializeAppContext(): void
    {
        preg_match(self::CLASS_REGEX, $this->getClass(), $matches);
        // Build route base name for frontend admin view
        // @see \Sonata\AdminBundle\Admin\AbstractAdmin::getBaseRouteName
        if (!$matches) {
            throw new RuntimeException(sprintf('Cannot automatically determine base route name, please define a default `baseRouteName` value for the admin class `%s`', static::class));
        }

        $routeName = sprintf('_%s%s_%s',
            empty($matches[1]) ? '' : $this->urlize($matches[1]) . '_',
            $this->urlize($matches[3]),
            $this->urlize($matches[5])
        );
        $this->baseRouteName = 'frontend' . $routeName;

        $routePattern = sprintf(
            '/%s%s/%s',
            empty($matches[1]) ? '' : $this->urlize($matches[1], '-') . '/',
            $this->urlize($matches[3], '-'),
            $this->urlize($matches[5], '-')
        );
        $this->baseRoutePattern = '/frontend' . $routePattern;
        $this->adminBaseRouteName = $routeName;
        $this->adminBaseRoutePattern = $routePattern;
        $templateRegistry = $this->getTemplateRegistry();
        $listTemplate = $templateRegistry->getTemplate('list');
        if (strpos($listTemplate, '@SonataAdmin') === 0) {
            $templateRegistry->setTemplate('list', 'Frontend/Admin/CRUD/list.html.twig');
        }
        $templateRegistry->setTemplate('layout', 'Frontend/Admin/base.html.twig');
    }

    protected function addDefaultListActions(ListMapper $list): void
    {
        $list->add(ListMapper::NAME_ACTIONS, null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'show' => [],
            ]
        ]);
    }

    /**
     * Override the label for the default group by calling this function before any fields have been added
     * The label should be overridden, of the
     *
     * @param ShowMapper $show
     */
    final protected function setDefaultShowGroupLabel(ShowMapper $show): void
    {
        $groups = $this->getShowGroups();
        if (!$groups) {
            $translatorStrategy = $this->getLabelTranslatorStrategy();
            $show->with('default', [
                'auto_created' => true,
                'label' => $translatorStrategy->getLabel($this->defaultShowGroupLabel)
            ]);
        }
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $this->setDefaultShowGroupLabel($show);
    }

    /*
     * TODO: use admin routes instead of disabling routes (see hasRoute)
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        if ($this->adminBaseRoutePattern) {
            $adminRoutesCollection = new RouteCollection(
                \App\Admin\ServiceAdmin::class,
                $this->adminBaseRouteName,
                $this->adminBaseRoutePattern,
                $this->getBaseControllerName()
            );
            $adminRoutesCollection->add('create');
            $adminRoutesCollection->add('edit');
            $adminRoutesCollection->add('delete');
            $adminRoutesCollection->add('batch');
            $collection->addCollection($adminRoutesCollection);
        }
    }*/

    /**
     * @phpstan-param T $object
     */
    protected function prePersist(object $object): void
    {
        // Prevent any changes in frontend
        exit;
    }

    /**
     * @phpstan-param T $object
     */
    protected function postPersist(object $object): void
    {
        // Prevent any changes in frontend
        exit;
    }

    /**
     * @phpstan-param T $object
     */
    protected function preRemove(object $object): void
    {
        // Prevent any changes in frontend
        exit;
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['createdBy', 'modifiedBy']);
        return $settings;
    }

    public function generateContextObjectUrl(string $name, object $object, array $parameters = [], int $referenceType = RoutingUrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ($name === 'show' && $object instanceof SluggableInterface) {
            if (empty($parameters['slug'])) {
                /** @var SluggableInterface|BaseEntityInterface $object */
                $parameters['slug'] = $object->getSlug() ?? (string)$object->getId();
                unset($parameters['id']);
            }
        } else {
            $parameters['id'] = $this->getUrlSafeIdentifier($object);
        }

        return $this->generateUrl($name, $parameters, $referenceType);
    }

    abstract public function getRoutePrefix(): string;

    /**
     * Configures a list of default sort values.
     *
     * @phpstan-param array{_page?: int, _per_page?: int, _sort_by?: string, _sort_order?: string} $sortValues
     * @param array $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $sortValues[DatagridInterface::SORT_ORDER] = $sortValues[DatagridInterface::SORT_ORDER] ?? 'ASC';
            $sortValues[DatagridInterface::SORT_BY] = $sortValues[DatagridInterface::SORT_BY] ?? 'name';
        }
    }
}
