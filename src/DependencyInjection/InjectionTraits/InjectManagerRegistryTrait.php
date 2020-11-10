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

namespace App\DependencyInjection\InjectionTraits;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

trait InjectManagerRegistryTrait
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @required
     * @param ManagerRegistry $registry
     */
    public function injectManagerRegistry(ManagerRegistry $registry): void
    {
        $this->registry = $registry;
    }

    /**
     * Gets a named object manager.
     *
     * @param string|null $name The object manager name (null for the default one).
     *
     * @return EntityManager|ObjectManager
     */
    public function getEntityManager(string $name = null): ObjectManager
    {
        return $this->registry->getManager($name);
    }
}