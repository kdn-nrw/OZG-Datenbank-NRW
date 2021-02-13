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
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class jurisdiction
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_jurisdiction")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class Jurisdiction extends BaseNamedEntity
{
    public const TYPE_COUNTRY = 1;
    public const TYPE_STATE = 2;
    public const TYPE_COMMUNE = 3;

    /**
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="jurisdictions")
     */
    private $services;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ServiceSystem", mappedBy="jurisdictions")
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->serviceSystems = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem): self
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
    public function removeServiceSystem($serviceSystem): self
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

    /**
     * @param Service $service
     * @return self
     */
    public function addService($service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addJurisdiction($this);
        }

        return $this;
    }

    /**
     * @param Service $service
     * @return self
     */
    public function removeService($service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            $service->removeJurisdiction($this);
        }

        return $this;
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
