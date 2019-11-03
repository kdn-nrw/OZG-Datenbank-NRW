<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class jurisdiction
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_jurisdiction")
 * @ORM\HasLifecycleCallbacks
 */
class Jurisdiction extends BaseNamedEntity
{
    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceSystem", mappedBy="jurisdictions")
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->serviceSystems = new ArrayCollection();
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem)
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addJurisdiction($this);
        }

        return $this;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function removeServiceSystem($serviceSystem)
    {
        if ($this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->removeElement($serviceSystem);
            $serviceSystem->removeJurisdiction($this);
        }

        return $this;
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
