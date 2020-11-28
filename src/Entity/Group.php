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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseGroup;

/**
 * User group entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_user_group")
 */
class Group extends BaseGroup
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
     * Groups can have many sub groups.
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
     * Group constructor.
     *
     * @param string $name
     * @param array $roles
     */
    public function __construct($name, $roles = array())
    {
        parent::__construct($name, $roles);
        $this->subGroups = new ArrayCollection();
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
     * @param Group $group
     * @return self
     */
    public function addChild($group): self
    {
        if (!$this->subGroups->contains($group) && $group->getId() !== $this->getId()) {
            $this->subGroups->add($group);
        }

        return $this;
    }

    /**
     * @param Group $group
     * @return self
     */
    public function removeChild($group): self
    {
        if ($this->subGroups->contains($group)) {
            $this->subGroups->removeElement($group);
        }

        return $this;
    }

    /**
     * @return Group[]|ArrayCollection
     */
    public function getSubGroups()
    {
        if (null === $this->subGroups) {
            $this->subGroups = new ArrayCollection();
        }
        return $this->subGroups;
    }

    /**
     * @param Group[]|ArrayCollection $subGroups
     */
    public function setSubGroups($subGroups): void
    {
        $this->subGroups = $subGroups;
    }
}
