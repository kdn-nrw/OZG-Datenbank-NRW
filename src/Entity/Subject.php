<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Subject (Themenfeld)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_subject")
 * @ORM\HasLifecycleCallbacks
 */
class Subject extends BaseNamedEntity
{

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
