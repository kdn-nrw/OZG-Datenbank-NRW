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
 * Class Subject (Themenfeld)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_subject")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Subject extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * @var Situation[]|Collection
     * @ORM\OneToMany(targetEntity="Situation", mappedBy="subject", cascade={"all"})
     */
    private $situations;

    public function __construct()
    {
        $this->situations = new ArrayCollection();
    }

    /**
     * @return Situation[]|Collection
     */
    public function getSituations()
    {
        return $this->situations;
    }

    /**
     * @param Situation[]|Collection $situations
     */
    public function setSituations($situations): void
    {
        $this->situations = $situations;
    }

}
