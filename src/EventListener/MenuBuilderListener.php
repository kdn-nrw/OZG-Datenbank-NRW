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


use App\Translator\TranslatorAwareTrait;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MenuBuilderListener
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class MenuBuilderListener
{
    use TranslatorAwareTrait;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Security
     */
    private $security;

    /**
     * MenuBuilderListener constructor.
     * @param RequestStack $requestStack
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(RequestStack $requestStack, Security $security, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->setTranslator($translator);
    }

    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        // standard controller route is not marked as current in menu
        $request = $this->requestStack->getMasterRequest();
        $currentRoute = null !== $request ? $request->get('_route') : '_no_route';
        $this->moveSolutionMenuToTop($menu, $currentRoute);
        $this->addSearchNode($menu, $currentRoute);
        $isSuperAdmin = $this->security->isGranted('ROLE_SUPER_ADMIN');
        if ($isSuperAdmin || $this->security->isGranted('ROLE_VSM')) {
            $this->addVsmNodes($menu, $currentRoute);
        }
        $this->moveContactMenuToTop($menu, $currentRoute);
        $this->updateOnboardingMenu($menu, $currentRoute);
        $this->setNavItemClasses($menu);
    }

    /**
     * Add extra level in onboarding navigation
     *
     * @param ItemInterface $menu
     * @param string $currentRoute
     * @return void
     */
    private function updateOnboardingMenu(ItemInterface $menu, string $currentRoute): void
    {
        $groupNode = $menu->getChild('app.onboarding_group');
        if (null !== $groupNode) {
            $groupNode->removeChild('app.dataclearing.list');
            $groupNode->removeChild('app.inquiry.list');
            $subNavGroups = [
                'app.menu.onboarding_submenu_1' => [],
                'app.menu.onboarding_submenu_2' => [],
            ];
            $moveToSecondItem = ['app.xta_server.list', ];//'app.monument_authority.list'
            $groupNode->removeChild('app.monument_authority.list');
            foreach ($groupNode->getChildren() as $child) {
                $sNr = in_array($child->getName(), $moveToSecondItem, false) ? 2 : 1;
                $subNavGroups['app.menu.onboarding_submenu_' . $sNr][] = $child;
            }
            foreach ($subNavGroups as $key => $children) {
                $subNavNode = $groupNode->addChild($key, [
                    // Translate label here and set "safe_label", so soft hyphen (&shy;) in translation will not be escaped
                    'label' => $this->translate($key),
                    //'route' => 'app_vsm_snippet',
                ]);
                $subNavNode->setExtra('safe_label', true);

                foreach ($children as $child) {
                    $groupNode->removeChild($child);
                    $subNavNode->addChild($child);
                }
                if (count($subNavNode->getChildren()) === 0) {
                    $groupNode->removeChild($key);
                }
            }
        }
    }

    /**
     * @param ItemInterface $menu
     * @return void
     */
    private function setNavItemClasses(ItemInterface $menu)
    {
        if ($menu->hasChildren()) {
            foreach ($menu->getChildren() as $child) {
                $this->setNavItemClasses($child);
            }
        } else {
            $navItemClass = $menu->getAttribute('class', '');
            $menu->setAttribute('class', trim($navItemClass . ' nav-item'));
            $menu->setAttribute('data-name', $menu->getName());
            $navLinkClass = $menu->getLinkAttribute('class', '');
            $menu->setLinkAttribute('class', trim($navLinkClass . ' nav-link'));

        }
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

    private function addVsmNodes(ItemInterface $menu, string $currentRoute): void
    {
        $groupNode = $menu->getChild('app.ozg_implementation_group');
        if (null !== $groupNode) {
            $vsmChild = $menu->addChild('app.vsm_group', [
                // Translate label here and set "safe_label", so soft hyphen (&shy;) in translation will not be escaped
                'label' => $this->translate('app.menu.vsm_group'),
                //'route' => 'app_vsm_snippet',
            ]);
            $vsmChild->setExtra('safe_label', true);
            $childNode = $vsmChild->addChild('app.vsm_api', [
                'label' => 'app.menu.vsm_api',
                'route' => 'app_vsm_api_index',
            ]);
            $childNode->setExtras([
                'icon' => '<i class="fas fa-search" aria-hidden="true"></i>',
            ]);
            $childNode = $vsmChild->addChild('app.vsm_snippet', [
                'label' => 'app.menu.vsm_snippet',
                'route' => 'app_vsm_snippet',
            ]);
            $childNode->setExtras([
                'icon' => '<i class="fas fa-search" aria-hidden="true"></i>',
            ]);
            $childNode = $vsmChild->addChild('app.vsm_snippet_map', [
                'label' => 'app.menu.vsm_snippet_map',
                'route' => 'app_vsm_snippet_map',
            ]);
            $childNode->setExtras([
                'icon' => '<i class="fas fa-search" aria-hidden="true"></i>',
            ]);
            $groupNode->removeChild($vsmChild);
            $this->addChildToGroup(
                $menu,
                $currentRoute,
                $vsmChild,
                'search',
                'fa-search',
                'app_vsm'
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
            'app.state_group',
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
        ?string $icon,
        ?string $activeRoutePrefix
    ): void
    {
        if (null !== $childNode) {
            if ($icon) {
                $childNode->setExtra('icon', '<i class="fas ' . $icon . '" aria-hidden="true"></i>');
            }
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
            if ($activeRoutePrefix && strpos($currentRoute, $activeRoutePrefix) !== false) {
                $childNode->setCurrent(true);
            }

            $parentNode->setChildren($newChildren);
        }
    }
}
