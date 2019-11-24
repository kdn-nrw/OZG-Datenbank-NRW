<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 */

namespace App\Admin\Frontend;


use Sonata\AdminBundle\Admin\AbstractAdmin;

/**
 * Class AbstractFrontendAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 */
abstract class AbstractFrontendAdmin extends AbstractAdmin
{
    use FrontendTranslatorStrategyTrait;

    const APP_CONTEXT_BE = 'backend';
    const APP_CONTEXT_FE = 'frontend';

    protected $appContext = self::APP_CONTEXT_BE;

    protected $adminBaseRouteName;
    protected $adminBaseRoutePattern;

    /**
     * @param string $appContext
     */
    public function setAppContext(string $appContext): void
    {
        $this->appContext = $appContext;
        if ($this->isFrontend()) {
            preg_match(self::CLASS_REGEX, $this->getClass(), $matches);
            // Build route base name for frontend admin view
            // @see \Sonata\AdminBundle\Admin\AbstractAdmin::getBaseRouteName
            if (!$matches) {
                throw new \RuntimeException(sprintf('Cannot automatically determine base route name, please define a default `baseRouteName` value for the admin class `%s`', static::class));
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
            $this->setTemplate('list', 'Frontend/Admin/CRUD/list.html.twig');
            $this->setTemplate('layout', 'Frontend/Admin/base.html.twig');
        }
    }

    protected function isFrontend()
    {
        return $this->appContext == self::APP_CONTEXT_FE;
    }

    public function hasRoute($name)
    {
        if (in_array($name, ['batch', 'create', 'edit', 'delete'])) {
            return false;
        }
        return parent::hasRoute($name);
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

}
