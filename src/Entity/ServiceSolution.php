<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;


/**
 * Class service solution
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_solution")
 * @ORM\HasLifecycleCallbacks
 */
class ServiceSolution extends BaseEntity
{
    use HideableEntityTrait;

    /**
     * @var Service
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="solutions", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $service;

    /**
     * @var Solution
     * @ORM\ManyToOne(targetEntity="Solution", inversedBy="serviceSolutions", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $solution;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @var Maturity|null
     *
     * @ORM\ManyToOne(targetEntity="Maturity", cascade={"persist"})
     * @ORM\JoinColumn(name="maturity_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $maturity;

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    /**
     * @return Solution
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution($solution): void
    {
        $this->solution = $solution;
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
     * @return Status|null
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @param Status|null $status
     */
    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Maturity|null
     */
    public function getMaturity()
    {
        return $this->maturity;
    }

    /**
     * @param Maturity|null $maturity
     */
    public function setMaturity($maturity): void
    {
        $this->maturity = $maturity;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $service = $this->getService();
        if (null === $service) {
            $name = $this->getId();
        } else {
            $name = '';
            $serviceSystem = $service->getServiceSystem();
            if (null !== $serviceSystem) {
                $name = 'Leistungsbündel ' . $serviceSystem->getName() .': Leistung ';
            }
            $name .= $service->getName();
        }
        if (null !== $maturity = $this->getMaturity()) {
            $name .= ' (Reifegrad: '.$this->getMaturity()->getName().')';
        }
        return $name;
    }

}
