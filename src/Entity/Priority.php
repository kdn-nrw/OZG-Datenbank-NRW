<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityInterface;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityTrait;


/**
 * Class Priority
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_priority")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Priority extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * priolv
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 0;

    /**
     * priolverkl
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\OneToMany(targetEntity="ServiceSystem", mappedBy="priority")
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->serviceSystems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ServiceSystem[]|Collection
     */
    public function getServiceSystems()
    {
        return $this->serviceSystems;
    }

    /**
     * @param ServiceSystem[]|Collection $serviceSystems
     */
    public function setServiceSystems($serviceSystems): void
    {
        $this->serviceSystems = $serviceSystems;
    }

}
