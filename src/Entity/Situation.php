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
 * Class LAGE
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_situation")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Situation extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * @var Subject
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="situations", cascade={"persist"})
     */
    private $subject;

    /**
     * @var Service[]|Collection
     * @ORM\OneToMany(targetEntity="Service", mappedBy="situation")
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return Service[]|Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Service[]|Collection $services
     */
    public function setServices($services): void
    {
        $this->services = $services;
    }

}
