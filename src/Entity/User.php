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

namespace App\Entity;

use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * User entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_user_user")
 */
class User extends BaseUser
{
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Organisation|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $organisation;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune")
     * @ORM\JoinTable(name="ozg_user_commune_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $communes;

    /**
     * @var ModelRegion[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ModelRegion")
     * @ORM\JoinTable(name="ozg_user_model_region_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="model_region_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $modelRegions;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider")
     * @ORM\JoinTable(name="ozg_user_service_provider_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->communes = new ArrayCollection();
        $this->modelRegions = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Hook on pre-persist operations.
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        parent::prePersist();
        // Update FOS user roles (array is generated in getter function)
        $this->setRoles($this->getRoles());
    }

    /**
     * Hook on pre-update operations.
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        parent::preUpdate();
        // Update FOS user roles (array is generated in getter function)
        $this->setRoles($this->getRoles());
    }

    /**
     * We only use groups for setting the roles. Therefore the roles array must be cleared
     * before adding the group roles!
     *
     * {@inheritdoc}
     */
    public function getRoles()
    {
        //$roles = $this->roles;
        $roles = [];

        foreach ($this->getGroups() as $group) {
            /** @var Group $group */
            $this->addGroupRolesRecursive($group, $roles);
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Adds the roles for the given group and all sub-groups recursively
     *
     * @param Group $group The user group entity
     * @param array $roles The role list for the current user
     * @param array|int[] $addedGroupIds List of group ids that were already added, to prevent infinite loops
     */
    private function addGroupRolesRecursive(Group $group, array &$roles, array $addedGroupIds = []): void
    {
        if (!in_array($group->getId(), $addedGroupIds, true)) {
            $addedGroupIds[] = $group->getId();
            $groupRoles = $group->getRoles();
            foreach ($groupRoles as $role) {
                if (strpos($role, 'ROLE_MINDBASE') === false) {
                    $roles[] = $role;
                }
            }
            $subGroups = $group->getSubGroups();
            foreach ($subGroups as $subGroup) {
                /** @var Group $subGroup */
                $this->addGroupRolesRecursive($subGroup, $roles, $addedGroupIds);
            }
        }
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function removeCommune($commune): self
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        if (null === $this->communes) {
            $this->communes = new ArrayCollection();
        }
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function addModelRegion($modelRegion): self
    {
        if (!$this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->add($modelRegion);
        }

        return $this;
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function removeModelRegion($modelRegion): self
    {
        if ($this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->removeElement($modelRegion);
        }

        return $this;
    }

    /**
     * @return ModelRegion[]|Collection
     */
    public function getModelRegions()
    {
        return $this->modelRegions;
    }

    /**
     * @param ModelRegion[]|Collection $modelRegions
     */
    public function setModelRegions($modelRegions): void
    {
        $this->modelRegions = $modelRegions;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider($serviceProvider): self
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $serviceProviders
     */
    public function setServiceProviders($serviceProviders): void
    {
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * @return Organisation|null
     */
    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    /**
     * @param Organisation|null $organisation
     */
    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

}
