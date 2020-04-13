<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */

namespace App\EventListener;


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
        $groupNode = $menu->getChild('app.ozg_implementation_group');
        if (null !== $groupNode) {
            $childSolution = $groupNode->getChild('app.solution.list');
            if (null !== $childSolution) {
                $childSolution->setExtras([
                    'icon' => '<i class="fa fa-lightbulb-o" aria-hidden="true"></i>',
                ]);
            }
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
            ])->setExtras([
                'icon' => '<i class="fa fa-search" aria-hidden="true"></i>',
            ]);
        }
        // standard controller route is not marked as current in menu
        $currentRoute = $this->requestStack->getMasterRequest()->get('_route');
        $childAsCurrentRoutes = ['app_search_list'];
        if (in_array($currentRoute, $childAsCurrentRoutes)) {
            $child->setCurrent(true);
        }

        $groupNode = $menu->getChild('app.implementation_group');
        $childContact = $groupNode->getChild('app.contact.list');
        if (null !== $childContact) {
            $groupNode->removeChild('app.contact.list');
            $childContact->setExtras([
                'icon' => '<i class="fa fa-address-card" aria-hidden="true"></i>',
            ]);
            $newChildren = [];
            $children = $menu->getChildren();
            $wasAdded = false;
            foreach ($children as $child) {
                $newChildren[$child->getName()] = $child;
                if ($child->getName() === 'app.service_group') {
                    $newChildren[$childContact->getName()] = $childContact;
                    $wasAdded = true;
                }
            }
            if (!$wasAdded) {
                $newChildren[$childContact->getName()] = $childContact;
            }
            if (strpos($currentRoute, 'admin_app_contact') !== false) {
                $childContact->setCurrent(true);
            }

            $menu->setChildren($newChildren);
        }
    }
}
