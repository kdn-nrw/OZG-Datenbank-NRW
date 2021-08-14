<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DependencyInjection\InjectionTraits;


use Symfony\Component\Routing\RouterInterface;

/**
 * Trait InjectRouterTrait
 */
trait InjectRouterTrait
{

    /**
     * @var RouterInterface
     */
    private $injectedRouter;

    /**
     * @return RouterInterface
     */
    protected function getRouter(): RouterInterface
    {
        return $this->injectedRouter;
    }

    /**
     * @required
     * @param RouterInterface $injectedRouter
     */
    public function injectRouter(RouterInterface $injectedRouter): void
    {
        $this->injectedRouter = $injectedRouter;
    }

}
