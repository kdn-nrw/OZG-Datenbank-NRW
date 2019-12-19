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
    const TYPE_COUNTRY = 1;
    const TYPE_STATE = 2;
    const TYPE_COMMUNE = 3;

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

    /*
    public static function createForType($type)
    {
        $entity = null;
        switch ($type) {
            case self::TYPE_COUNTRY;
                $entity = new Jurisdiction();
                $entity->setId(self::TYPE_COUNTRY);
                $entity->setName('Bund');
                break;
            case self::TYPE_STATE;
                $entity = new Jurisdiction();
                $entity->setId(self::TYPE_STATE);
                $entity->setName('Land');
                break;
            case self::TYPE_COMMUNE;
                $entity = new Jurisdiction();
                $entity->setId(self::TYPE_COMMUNE);
                $entity->setName('Kommunal');
                break;
        }
        return $entity;
    }*/
}
