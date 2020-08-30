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
use RuntimeException;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as RoutingUrlGeneratorInterface;

/**
 * Class AbstractFrontendAdmin
 */
abstract class AbstractFrontendAdmin extends AbstractContextAwareAdmin implements ContextFrontendAdminInterface
{
    use FrontendTranslatorStrategyTrait;

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
     * Exclude fields in export
     */
    protected $excludeExportFields = ['createdBy', 'modifiedBy', 'hidden'];

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
        $listTemplate = $this->getTemplateRegistry()->getTemplate('list');
        if (strpos($listTemplate, '@SonataAdmin') === 0) {
            $this->setTemplate('list', 'Frontend/Admin/CRUD/list.html.twig');
        }
        $this->setTemplate('layout', 'Frontend/Admin/base.html.twig');
    }

    /**
     * Excludes all routes for that require role permissions
     *
     * @param string $name
     * @return bool
     */
    public function hasRoute($name)
    {
        if (in_array($name, ['batch', 'create', 'edit', 'delete'])) {
            return false;
        }
        return parent::hasRoute($name);
    }

    protected function addDefaultListActions(ListMapper $listMapper): void
    {
        $listMapper->add('_action', null, [
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

    protected function configureShowFields(ShowMapper $show)
    {
        $this->setDefaultShowGroupLabel($show);
    }

    /*
     * TODO: use admin routes instead of disabling routes (see hasRoute)
    protected function configureRoutes(RouteCollection $collection)
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

    public function update($object)
    {
        // disable update in frontend completely!
        return $object;
    }

    public function create($object)
    {
        // disable create in frontend completely!
        return $object;
    }

    public function delete($object)
    {
        // disable delete in frontend completely!
    }

    /**
     * @return array
     */
    protected function getExportExcludeFields(): array
    {
        return $this->excludeExportFields;
    }

    public function generateUrl($name, array $parameters = [], $absolute = RoutingUrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if (in_array($name, ['show', 'list', 'export'], false)) {
            $route = $this->getRoutePrefix() . '_' . $name;
            return $this->routeGenerator->generate($route, $parameters, $absolute);
        }
        return parent::generateUrl($name, $parameters, $absolute);
    }

    abstract protected function getRoutePrefix(): string;
}
