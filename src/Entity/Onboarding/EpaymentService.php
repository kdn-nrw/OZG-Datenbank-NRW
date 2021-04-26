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

use App\Entity\Base\BaseEntity;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\SortableEntityInterface;
use App\Entity\Base\SortableEntityTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class epayment service
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_epayment_service")
 * @ORM\HasLifecycleCallbacks
 */
class EpaymentService extends BaseEntity implements HideableEntityInterface, SortableEntityInterface
{
    use HideableEntityTrait;
    use SortableEntityTrait;

    /**
     * @var OnboardingService|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\OnboardingService", inversedBy="epaymentServices", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @var Epayment|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\Epayment", inversedBy="services", cascade={"persist"})
     * @ORM\JoinColumn(name="epayment_id", referencedColumnName="id")
     */
    private $epayment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="booking_text", type="text", nullable=true)
     */
    private $bookingText;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_first_account_assignment_information", type="text", nullable=true)
     */
    private $valueFirstAccountAssignmentInformation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_second_account_assignment_information", type="text", nullable=true)
     */
    private $valueSecondAccountAssignmentInformation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cost_unit", type="text", nullable=true)
     */
    private $costUnit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payers", type="text", nullable=true)
     */
    private $payers;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_description", type="text", nullable=true)
     */
    private $productDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tax_number", type="text", nullable=true)
     */
    private $taxNumber;

    /**
     * @return OnboardingService|null
     */
    public function getService(): ?OnboardingService
    {
        return $this->service;
    }

    /**
     * @param OnboardingService $service
     */
    public function setService(OnboardingService $service): void
    {
        $this->service = $service;
    }

    /**
     * @return Epayment|null
     */
    public function getEpayment(): ?Epayment
    {
        return $this->epayment;
    }

    /**
     * @param Epayment $epayment
     */
    public function setEpayment(Epayment $epayment): void
    {
        $this->epayment = $epayment;
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
     * @return string|null
     */
    public function getBookingText(): ?string
    {
        return $this->bookingText;
    }

    /**
     * @param string|null $bookingText
     */
    public function setBookingText(?string $bookingText): void
    {
        $this->bookingText = $bookingText;
    }

    /**
     * @return string|null
     */
    public function getValueFirstAccountAssignmentInformation(): ?string
    {
        return $this->valueFirstAccountAssignmentInformation;
    }

    /**
     * @param string|null $valueFirstAccountAssignmentInformation
     */
    public function setValueFirstAccountAssignmentInformation(?string $valueFirstAccountAssignmentInformation): void
    {
        $this->valueFirstAccountAssignmentInformation = $valueFirstAccountAssignmentInformation;
    }

    /**
     * @return string|null
     */
    public function getValueSecondAccountAssignmentInformation(): ?string
    {
        return $this->valueSecondAccountAssignmentInformation;
    }

    /**
     * @param string|null $valueSecondAccountAssignmentInformation
     */
    public function setValueSecondAccountAssignmentInformation(?string $valueSecondAccountAssignmentInformation): void
    {
        $this->valueSecondAccountAssignmentInformation = $valueSecondAccountAssignmentInformation;
    }

    /**
     * @return string|null
     */
    public function getCostUnit(): ?string
    {
        return $this->costUnit;
    }

    /**
     * @param string|null $costUnit
     */
    public function setCostUnit(?string $costUnit): void
    {
        $this->costUnit = $costUnit;
    }

    /**
     * @return string|null
     */
    public function getPayers(): ?string
    {
        return $this->payers;
    }

    /**
     * @param string|null $payers
     */
    public function setPayers(?string $payers): void
    {
        $this->payers = $payers;
    }

    /**
     * @return string|null
     */
    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

    /**
     * @param string|null $productDescription
     */
    public function setProductDescription(?string $productDescription): void
    {
        $this->productDescription = $productDescription;
    }

    /**
     * @return string|null
     */
    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    /**
     * @param string|null $taxNumber
     */
    public function setTaxNumber(?string $taxNumber): void
    {
        $this->taxNumber = $taxNumber;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = '';
        $project = $this->getEpayment();
        $service = $this->getService();
        if (null !== $project) {
            $name = $project . '';
        }
        if (null !== $service) {
            $name .= $service . '';
        }
        if (empty($name)) {
            $name = (string)$this->getId();
        }
        return $name;
    }

}
