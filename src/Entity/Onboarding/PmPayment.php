<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Onboarding;

use App\Entity\AddressTrait;
use App\Entity\Base\ContactPropertiesTrait;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class pmPayment
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_pm_payment")
 */
class PmPayment extends AbstractOnboardingEntity
{
    use AddressTrait;
    use ContactPropertiesTrait;

    /**
     * @ORM\Column(type="string", name="endpoint_system_test", length=255, nullable=true)
     * @var string|null
     */
    protected $endpointSystemTest;
    /**
     * @ORM\Column(type="string", name="password_system_test", length=255, nullable=true)
     * @var string|null
     */
    protected $passwordSystemTest;
    /**
     * @ORM\Column(type="string", name="endpoint_system_production", length=255, nullable=true)
     * @var string|null
     */
    protected $endpointSystemProduction;
    /**
     * @ORM\Column(type="string", name="password_system_production", length=255, nullable=true)
     * @var string|null
     */
    protected $passwordSystemProduction;

    /**
     * @var PmPaymentService[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\PmPaymentService", mappedBy="pmPayment", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     * @Assert\Valid()
     */
    protected $pmPaymentServices;

    public function __construct(Commune $commune)
    {
        parent::__construct($commune);
        $this->pmPaymentServices = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        if (!$this->street) {
            return $this->getCommune()->getStreet();
        }
        return $this->street;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        if (!$this->zipCode) {
            return $this->getCommune()->getZipCode();
        }
        return $this->zipCode;
    }

    /**
     * @param PmPaymentService $pmPaymentService
     * @return self
     */
    public function addPmPaymentService(PmPaymentService $pmPaymentService): self
    {
        if (!$this->pmPaymentServices->contains($pmPaymentService)) {
            $this->pmPaymentServices->add($pmPaymentService);
            $pmPaymentService->setPmPayment($this);
        }

        return $this;
    }

    /**
     * @param PmPaymentService $pmPaymentService
     * @return self
     */
    public function removePmPaymentService(PmPaymentService $pmPaymentService): self
    {
        if ($this->pmPaymentServices->contains($pmPaymentService)) {
            $this->pmPaymentServices->removeElement($pmPaymentService);
        }

        return $this;
    }

    /**
     * @return PmPaymentService[]|Collection
     */
    public function getPmPaymentServices()
    {
        return $this->pmPaymentServices;
    }

    /**
     * @param PmPaymentService[]|Collection $pmPaymentServices
     */
    public function setPmPaymentServices($pmPaymentServices): void
    {
        $this->pmPaymentServices = $pmPaymentServices;
    }

    /**
     * @return string|null
     */
    public function getEndpointSystemTest(): ?string
    {
        return $this->endpointSystemTest;
    }

    /**
     * @param string|null $endpointSystemTest
     */
    public function setEndpointSystemTest(?string $endpointSystemTest): void
    {
        $this->endpointSystemTest = $endpointSystemTest;
    }

    /**
     * @return string|null
     */
    public function getPasswordSystemTest(): ?string
    {
        return $this->passwordSystemTest;
    }

    /**
     * @param string|null $passwordSystemTest
     */
    public function setPasswordSystemTest(?string $passwordSystemTest): void
    {
        $this->passwordSystemTest = $passwordSystemTest;
    }

    /**
     * @return string|null
     */
    public function getEndpointSystemProduction(): ?string
    {
        return $this->endpointSystemProduction;
    }

    /**
     * @param string|null $endpointSystemProduction
     */
    public function setEndpointSystemProduction(?string $endpointSystemProduction): void
    {
        $this->endpointSystemProduction = $endpointSystemProduction;
    }

    /**
     * @return string|null
     */
    public function getPasswordSystemProduction(): ?string
    {
        return $this->passwordSystemProduction;
    }

    /**
     * @param string|null $passwordSystemProduction
     */
    public function setPasswordSystemProduction(?string $passwordSystemProduction): void
    {
        $this->passwordSystemProduction = $passwordSystemProduction;
    }

}
