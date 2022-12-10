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

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User group entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_user_group")
 */
class Group extends BaseNamedEntity implements GroupInterface
{

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    protected $name;

    /**
     * Groups can have many subgroups.
     *
     * @var ArrayCollection|Group[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="ozg_group_subgroup_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="sub_group_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    protected $subGroups;

    /**
     * @var array<int, string>
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles;

    /**
     * Group constructor.
     *
     * @param string $name
     * @param array $roles
     */
    public function __construct(string $name = '', array $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
        $this->subGroups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = strtoupper($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param GroupInterface $group
     * @return self
     */
    public function addChild(GroupInterface $group): self
    {
        if (!$this->subGroups->contains($group) && $group->getId() !== $this->getId()) {
            $this->subGroups->add($group);
        }

        return $this;
    }

    /**
     * @param GroupInterface $group
     * @return self
     */
    public function removeChild(GroupInterface $group): self
    {
        if ($this->subGroups->contains($group)) {
            $this->subGroups->removeElement($group);
        }

        return $this;
    }

    /**
     * @return GroupInterface[]|ArrayCollection
     */
    public function getSubGroups()
    {
        if (null === $this->subGroups) {
            $this->subGroups = new ArrayCollection();
        }
        return $this->subGroups;
    }

    /**
     * @param GroupInterface[]|ArrayCollection $subGroups
     */
    public function setSubGroups($subGroups): void
    {
        $this->subGroups = $subGroups;
    }
}
