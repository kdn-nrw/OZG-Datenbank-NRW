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

namespace App\EventListener;


use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MenuBuilderListener
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class MenuBuilderListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * MenuBuilderListener constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        // standard controller route is not marked as current in menu
        $request = $this->requestStack->getMasterRequest();
        $currentRoute = null !== $request ? $request->get('_route') : '_no_route';

        $this->moveSolutionMenuToTop($menu, $currentRoute);
        $this->addSearchNode($menu, $currentRoute);

        $this->moveContactMenuToTop($menu, $currentRoute);
    }

    private function addSearchNode(ItemInterface $menu, string $currentRoute): void
    {
        $groupNode = $menu->getChild('app.ozg_implementation_group');
        if (null !== $groupNode) {
            $child = $groupNode->addChild('search', [
                'label' => 'app.search.list',
                'route' => 'app_search_list',
                /*'extras' => [
                    'routes' => [
                        [
                            'route' => 'app_search_list',
                        ],
                    ],
                ],*/
            ]);
            $groupNode->removeChild($child);
            $this->addChildToGroup(
                $menu,
                $currentRoute,
                $child,
                'app_admin.menu.basic',
                'fa-search',
                'app_search'
            );
        }
    }

    private function moveContactMenuToTop(ItemInterface $menu, string $currentRoute): void
    {
        $this->moveChildNodeToTop(
            $menu,
            $currentRoute,
            $menu->getChild('app.implementation_group'),
            'app.contact.list',
            'app.service_group',
            'fa-address-card',
            'admin_app_contact'

        );
    }

    private function moveSolutionMenuToTop(ItemInterface $menu, string $currentRoute): void
    {
        $this->moveChildNodeToTop(
            $menu,
            $currentRoute,
            $menu->getChild('app.ozg_implementation_group'),
            'app.solution.list',
            'app.settings_group',
            'fa-puzzle-piece',
            'admin_app_solution'

        );
    }

    private function moveChildNodeToTop(
        ItemInterface $menu,
        string $currentRoute,
        ?ItemInterface $groupNode,
        string $moveChild,
        ?string $moveAfterGroup,
        string $icon,
        string $activeRoutePrefix
    ): void
    {
        if (null !== $groupNode && null !== $childNode = $groupNode->getChild($moveChild)) {
            $groupNode->removeChild($moveChild);
            $this->addChildToGroup(
                $menu,
                $currentRoute,
                $childNode,
                $moveAfterGroup,
                $icon,
                $activeRoutePrefix
            );
        }
    }

    private function addChildToGroup(
        ItemInterface $parentNode,
        string $currentRoute,
        ?ItemInterface $childNode,
        ?string $moveAfterItem,
        string $icon,
        string $activeRoutePrefix
    ): void
    {
        if (null !== $childNode) {
            $childNode->setExtras([
                'icon' => '<i class="fa '.$icon.'" aria-hidden="true"></i>',
            ]);
            $childNode->setParent($parentNode);
            $newChildren = [];
            $wasAdded = false;
            if (null === $moveAfterItem) {
                $newChildren[$childNode->getName()] = $childNode;
                $wasAdded = true;
            }
            $children = $parentNode->getChildren();
            foreach ($children as $childGroup) {
                $newChildren[$childGroup->getName()] = $childGroup;
                if (!$wasAdded && $childGroup->getName() === $moveAfterItem) {
                    $newChildren[$childNode->getName()] = $childNode;
                    $wasAdded = true;
                }
            }
            if (!$wasAdded) {
                $newChildren[$childNode->getName()] = $childNode;
            }
            if (strpos($currentRoute, $activeRoutePrefix) !== false) {
                $childNode->setCurrent(true);
            }

            $parentNode->setChildren($newChildren);
        }
    }
}
