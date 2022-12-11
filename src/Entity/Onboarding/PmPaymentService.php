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
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\Solution;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class pmPayment service
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_pm_payment_service")
 * @ORM\HasLifecycleCallbacks
 */
class PmPaymentService extends BaseEntity implements
    HideableEntityInterface,
    SortableEntityInterface,
    HasMetaDateEntityInterface
{
    use HideableEntityTrait;
    use SortableEntityTrait;

    /**
     * @var Solution|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Solution", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     */
    protected $solution;

    /**
     * @var PmPayment|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\PmPayment", inversedBy="pmPaymentServices", cascade={"persist"})
     * @ORM\JoinColumn(name="pm_payment_id", referencedColumnName="id")
     */
    protected $pmPayment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_method_name", type="text", nullable=true)
     */
    protected $paymentMethodName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="payment_method_prefix", type="text", nullable=true)
     */
    protected $paymentMethodPrefix;

    /**
     * Payment method end nr
     *
     * @var int|null
     *
     * @ORM\Column(name="payment_method_start_nr", type="integer", nullable=true)
     */
    protected $paymentMethodStartNr;

    /**
     * Payment method end nr
     *
     * @var int|null
     *
     * @ORM\Column(name="payment_method_end_nr", type="integer", nullable=true)
     */
    protected $paymentMethodEndNr;


    /**
     * @return Solution|null
     */
    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution(Solution $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return PmPayment|null
     */
    public function getPmPayment(): ?PmPayment
    {
        return $this->pmPayment;
    }

    /**
     * @param PmPayment $pmPayment
     */
    public function setPmPayment(PmPayment $pmPayment): void
    {
        $this->pmPayment = $pmPayment;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethodName(): ?string
    {
        return $this->paymentMethodName;
    }

    /**
     * @param string|null $paymentMethodName
     */
    public function setPaymentMethodName(?string $paymentMethodName): void
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethodPrefix(): ?string
    {
        return $this->paymentMethodPrefix;
    }

    /**
     * @param string|null $paymentMethodPrefix
     */
    public function setPaymentMethodPrefix(?string $paymentMethodPrefix): void
    {
        $this->paymentMethodPrefix = $paymentMethodPrefix;
    }

    /**
     * @return int|null
     */
    public function getPaymentMethodStartNr(): ?int
    {
        return $this->paymentMethodStartNr;
    }

    /**
     * @param int|null $paymentMethodStartNr
     */
    public function setPaymentMethodStartNr(?int $paymentMethodStartNr): void
    {
        $this->paymentMethodStartNr = $paymentMethodStartNr;
    }

    /**
     * @return int|null
     */
    public function getPaymentMethodEndNr(): ?int
    {
        return $this->paymentMethodEndNr;
    }

    /**
     * @param int|null $paymentMethodEndNr
     */
    public function setPaymentMethodEndNr(?int $paymentMethodEndNr): void
    {
        $this->paymentMethodEndNr = $paymentMethodEndNr;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = '';
        $project = $this->getPmPayment();
        $solution = $this->getSolution();
        if (null !== $project) {
            $name = $project . '';
        }
        if (null !== $solution) {
            $name .= ($name ? ': ' : '') . $solution;
        }
        if (empty($name)) {
            $name = (string)$this->getId();
        }
        return $name;
    }

}
