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
use SimpleThings\EntityAudit\Collection\AuditedCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ePayBL
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_epayment")
 */
class Epayment extends AbstractOnboardingEntity
{
    public const DEFAULT_PAYMENT_PROVIDER = 'Girosolution';

    public const PAYMENT_TYPE_GIROPAY = 1;
    public const PAYMENT_TYPE_CREDIT_CARD = 2;
    public const PAYMENT_TYPE_PAYPAL = 3;
    public const PAYMENT_TYPE_PAY_DIREKT = 4;

    public static $paymentTypeChoices = [
        self::PAYMENT_TYPE_GIROPAY => 'app.epayment.groups.payment_type_choices.giropay',
        self::PAYMENT_TYPE_CREDIT_CARD => 'app.epayment.groups.payment_type_choices.credit_card',
        self::PAYMENT_TYPE_PAYPAL => 'app.epayment.groups.payment_type_choices.paypal',
        self::PAYMENT_TYPE_PAY_DIREKT => 'app.epayment.groups.payment_type_choices.paydirekt',
    ];

    use AddressTrait;
    use ContactPropertiesTrait;

    /**
     * @ORM\Column(type="string", name="payment_provider_account_id", length=255, nullable=true)
     * @var string|null
     */
    protected $paymentProviderAccountId;

    /**
     * Payment provider
     *
     * @var string|null
     *
     * @ORM\Column(type="string", name="payment_provider", length=255, nullable=true)
     */
    protected $paymentProvider = self::DEFAULT_PAYMENT_PROVIDER;

    /**
     * @var Contact|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\Contact", mappedBy="epayment", cascade={"all"})
     */
    protected $paymentUser;

    /**
     * @ORM\Column(type="string", name="mandator_email", length=255, nullable=true)
     * @var string|null
     */
    protected $mandatorEmail;

    /**
     * @ORM\Column(type="string", name="test_ip_address", length=255, nullable=true)
     * @var string|null
     */
    protected $testIpAddress;

    /**
     * Projects for this entity
     *
     * @var ArrayCollection|EpaymentProject[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\EpaymentProject", mappedBy="epayment", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $projects;

    /**
     * @ORM\Column(type="string", name="client_number_integration", length=255, nullable=true)
     * @var string|null
     */
    protected $clientNumberIntegration;

    /**
     * @ORM\Column(type="string", name="client_number_production", length=255, nullable=true)
     * @var string|null
     */
    protected $clientNumberProduction;

    /**
     * @ORM\Column(type="string", name="manager_number", length=255, nullable=true)
     * @var string|null
     */
    protected $managerNumber;

    /**
     * @ORM\Column(type="string", name="budget_office", length=255, nullable=true)
     * @var string|null
     */
    protected $budgetOffice;

    /**
     * @ORM\Column(type="string", name="object_number", length=255, nullable=true)
     * @var string|null
     */
    protected $objectNumber;

    /**
     * @ORM\Column(type="string", name="cash_register_personal_account_number", length=255, nullable=true)
     * @var string|null
     */
    protected $cashRegisterPersonalAccountNumber;

    /**
     * @ORM\Column(type="string", name="indicator_dunning_procedure", length=255, nullable=true)
     * @var string|null
     */
    protected $indicatorDunningProcedure;

    /**
     * @ORM\Column(type="string", name="booking_text", length=255, nullable=true)
     * @var string|null
     */
    protected $bookingText;

    /**
     * @ORM\Column(type="string", name="description_of_the_booking_list", length=255, nullable=true)
     * @var string|null
     */
    protected $descriptionOfTheBookingList;

    /**
     * @ORM\Column(type="string", name="manager_no", length=255, nullable=true)
     * @var string|null
     */
    protected $managerNo = 'BW0000';

    /**
     * @ORM\Column(type="string", name="application_name", length=255, nullable=true)
     * @var string|null
     */
    protected $applicationName = 'Kommunalportal';

    /**
     * @ORM\Column(type="integer", name="length_receipt_number", nullable=true)
     * @var int|null
     */
    protected $lengthReceiptNumber;

    /**
     * @ORM\Column(type="boolean", name="cash_register_check_procedure_status", nullable=true)
     * @var boolean
     */
    protected $cashRegisterCheckProcedureStatus = true;

    /**
     * @ORM\Column(type="integer", name="length_first_account_assignment_information", nullable=true)
     * @var int|null
     */
    protected $lengthFirstAccountAssignmentInformation;

    /**
     * @ORM\Column(name="content_first_account_assignment_information", type="text", nullable=true)
     * @var string|null
     */
    protected $contentFirstAccountAssignmentInformation;

    /**
     * @ORM\Column(type="integer", name="length_second_account_assignment_information", nullable=true)
     * @var int|null
     */
    protected $lengthSecondAccountAssignmentInformation;

    /**
     * @ORM\Column(name="content_second_account_assignment_information", type="text", nullable=true)
     * @var string|null
     */
    protected $contentSecondAccountAssignmentInformation;

    /**
     * @var ServiceProvider|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", cascade={"persist"})
     * @ORM\JoinColumn(name="payment_operator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $paymentOperator;

    /**
     * @var EpaymentService[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\EpaymentService", mappedBy="epayment", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     * @Assert\Valid()
     */
    protected $epaymentServices;

    /**
     * Toggle XFinance export
     *
     * @var bool
     *
     * @ORM\Column(name="xfinance_file_required", type="boolean")
     */
    protected $xFinanceFileRequired = true;

    /**
     * Selected days for XFinance
     * @var array|null
     *
     * @ORM\Column(name="xfinance_file_days", type="json", nullable=true)
     */
    protected $xFinanceFileDays = [];

    /**
     * Payment types for payment service provider
     *
     * @var array|int[]
     *
     * @ORM\Column(name="payment_types", type="simple_array", nullable=true)
     */
    protected $paymentTypes = [];

    public function __construct(Commune $commune)
    {
        parent::__construct($commune);
        $this->projects = new ArrayCollection();
        $this->epaymentServices = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getPaymentProviderAccountId(): ?string
    {
        return $this->paymentProviderAccountId;
    }

    /**
     * @param string|null $paymentProviderAccountId
     */
    public function setPaymentProviderAccountId(?string $paymentProviderAccountId): void
    {
        $this->paymentProviderAccountId = $paymentProviderAccountId;
    }

    /**
     * @return string|null
     */
    public function getPaymentProvider(): ?string
    {
        return $this->paymentProvider ?? self::DEFAULT_PAYMENT_PROVIDER;
    }

    /**
     * @param string|null $paymentProvider
     */
    public function setPaymentProvider(?string $paymentProvider): void
    {
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * @return Contact
     */
    public function getPaymentUser(): Contact
    {
        if (null === $this->paymentUser) {
            $this->paymentUser = new Contact($this, Contact::CONTACT_TYPE_EPAYMENT_USER);
        }
        return $this->paymentUser;
    }

    /**
     * @param Contact|null $paymentUser
     */
    public function setPaymentUser(?Contact $paymentUser): void
    {
        $this->paymentUser = $paymentUser;
    }

    /**
     * @return string|null
     */
    public function getMandatorEmail(): ?string
    {
        return $this->mandatorEmail;
    }

    /**
     * @param string|null $mandatorEmail
     */
    public function setMandatorEmail(?string $mandatorEmail): void
    {
        $this->mandatorEmail = $mandatorEmail;
    }

    /**
     * @return string|null
     */
    public function getTestIpAddress(): ?string
    {
        return $this->testIpAddress;
    }

    /**
     * @param string|null $testIpAddress
     */
    public function setTestIpAddress(?string $testIpAddress): void
    {
        $this->testIpAddress = $testIpAddress;
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
     * @return string|null
     */
    public function getTown(): ?string
    {
        if (!$this->town) {
            return $this->getCommune()->getTown();
        }
        return $this->town;
    }

    /**
     * @return EpaymentProject[]|ArrayCollection
     */
    public function getProjects(): Collection
    {
        $collection = $this->projects;
        // Prevent exception when loading audits; only used by AuditReader
        if ($collection instanceof AuditedCollection) {
            return $collection;
        }
        if ($collection instanceof Collection) {
            $projectTypeChoices = EpaymentProject::getTypeEnvironmentChoices();
            $order = [];
            $sorting = 1;
            $missingTypes = [];
            foreach ($projectTypeChoices as $typeKey => $typeData) {
                $order[$typeKey] = $sorting;
                $missingTypes[$typeKey] = $typeData;
                ++$sorting;
            }
            foreach ($collection as $entity) {
                $missingTypes[$entity->getTypeEnvironmentKey()] = false;
            }
            foreach (array_filter($missingTypes) as $typeData) {
                if (is_array($typeData)) {
                    $collection->add(new EpaymentProject($this, $typeData['provider_type'], $typeData['environment']));
                }
            }
            $iterator = $collection->getIterator();
            $iterator->uasort(static function (EpaymentProject $a, EpaymentProject $b) use ($order) {
                $orderA = $order[$a->getTypeEnvironmentKey()] ?? 999;
                $orderB = $order[$b->getTypeEnvironmentKey()] ?? 999;
                return ($orderA < $orderB) ? -1 : 1;
            });
            /** @var EpaymentProject[] $sortedEntities */
            $sortedEntities = iterator_to_array($iterator);
            $collection->clear();
            $addedTypes = [];
            foreach ($sortedEntities as $collectionEntity) {
                if ($addedTypes[$collectionEntity->getTypeEnvironmentKey()] ?? false) {
                    continue;
                }
                $collection->add($collectionEntity);
                $addedTypes[$collectionEntity->getTypeEnvironmentKey()] = true;
            }
        }
        return $this->projects;
    }

    /**
     * @param EpaymentProject[]|ArrayCollection $projects
     */
    public function setProjects($projects): void
    {
        $this->projects = $projects;
    }

    /**
     * @return string|null
     */
    public function getClientNumberIntegration(): ?string
    {
        return $this->clientNumberIntegration;
    }

    /**
     * @param string|null $clientNumberIntegration
     */
    public function setClientNumberIntegration(?string $clientNumberIntegration): void
    {
        $this->clientNumberIntegration = $clientNumberIntegration;
    }

    /**
     * @return string|null
     */
    public function getClientNumberProduction(): ?string
    {
        return $this->clientNumberProduction;
    }

    /**
     * @param string|null $clientNumberProduction
     */
    public function setClientNumberProduction(?string $clientNumberProduction): void
    {
        $this->clientNumberProduction = $clientNumberProduction;
    }

    /**
     * @return string|null
     */
    public function getManagerNumber(): ?string
    {
        return $this->managerNumber;
    }

    /**
     * @param string|null $managerNumber
     */
    public function setManagerNumber(?string $managerNumber): void
    {
        $this->managerNumber = $managerNumber;
    }

    /**
     * @return string|null
     */
    public function getBudgetOffice(): ?string
    {
        return $this->budgetOffice;
    }

    /**
     * @param string|null $budgetOffice
     */
    public function setBudgetOffice(?string $budgetOffice): void
    {
        $this->budgetOffice = $budgetOffice;
    }

    /**
     * @return string|null
     */
    public function getObjectNumber(): ?string
    {
        return $this->objectNumber;
    }

    /**
     * @param string|null $objectNumber
     */
    public function setObjectNumber(?string $objectNumber): void
    {
        $this->objectNumber = $objectNumber;
    }

    /**
     * @return string|null
     */
    public function getCashRegisterPersonalAccountNumber(): ?string
    {
        return $this->cashRegisterPersonalAccountNumber;
    }

    /**
     * @param string|null $cashRegisterPersonalAccountNumber
     */
    public function setCashRegisterPersonalAccountNumber(?string $cashRegisterPersonalAccountNumber): void
    {
        $this->cashRegisterPersonalAccountNumber = $cashRegisterPersonalAccountNumber;
    }

    /**
     * @return string|null
     */
    public function getIndicatorDunningProcedure(): ?string
    {
        return $this->indicatorDunningProcedure;
    }

    /**
     * @param string|null $indicatorDunningProcedure
     */
    public function setIndicatorDunningProcedure(?string $indicatorDunningProcedure): void
    {
        $this->indicatorDunningProcedure = $indicatorDunningProcedure;
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
    public function getDescriptionOfTheBookingList(): ?string
    {
        return $this->descriptionOfTheBookingList;
    }

    /**
     * @param string|null $descriptionOfTheBookingList
     */
    public function setDescriptionOfTheBookingList(?string $descriptionOfTheBookingList): void
    {
        $this->descriptionOfTheBookingList = $descriptionOfTheBookingList;
    }

    /**
     * @return string
     */
    public function getManagerNo(): string
    {
        return $this->managerNo ?? 'BW0000';
    }

    /**
     * @param string|null $managerNo
     */
    public function setManagerNo(?string $managerNo): void
    {
        $this->managerNo = $managerNo;
    }

    /**
     * @return string
     */
    public function getApplicationName(): string
    {
        return $this->applicationName ?? 'Kommunalportal';
    }

    /**
     * @param string|null $applicationName
     */
    public function setApplicationName(?string $applicationName): void
    {
        $this->applicationName = $applicationName;
    }

    /**
     * @return int|null
     */
    public function getLengthReceiptNumber(): ?int
    {
        return $this->lengthReceiptNumber;
    }

    /**
     * @param int|null $lengthReceiptNumber
     */
    public function setLengthReceiptNumber(?int $lengthReceiptNumber): void
    {
        $this->lengthReceiptNumber = $lengthReceiptNumber;
    }

    /**
     * @return bool
     */
    public function isCashRegisterCheckProcedureStatus(): bool
    {
        return $this->cashRegisterCheckProcedureStatus ?? true;
    }

    /**
     * @param bool $cashRegisterCheckProcedureStatus
     */
    public function setCashRegisterCheckProcedureStatus(bool $cashRegisterCheckProcedureStatus): void
    {
        $this->cashRegisterCheckProcedureStatus = $cashRegisterCheckProcedureStatus;
    }


    /**
     * @return int|null
     */
    public function getLengthFirstAccountAssignmentInformation(): ?int
    {
        return $this->lengthFirstAccountAssignmentInformation;
    }

    /**
     * @param int|null $lengthFirstAccountAssignmentInformation
     */
    public function setLengthFirstAccountAssignmentInformation(?int $lengthFirstAccountAssignmentInformation): void
    {
        $this->lengthFirstAccountAssignmentInformation = $lengthFirstAccountAssignmentInformation;
    }

    /**
     * @return int|null
     */
    public function getLengthSecondAccountAssignmentInformation(): ?int
    {
        return $this->lengthSecondAccountAssignmentInformation;
    }

    /**
     * @param int|null $lengthSecondAccountAssignmentInformation
     */
    public function setLengthSecondAccountAssignmentInformation(?int $lengthSecondAccountAssignmentInformation): void
    {
        $this->lengthSecondAccountAssignmentInformation = $lengthSecondAccountAssignmentInformation;
    }

    /**
     * @return string|null
     */
    public function getContentFirstAccountAssignmentInformation(): ?string
    {
        return $this->contentFirstAccountAssignmentInformation;
    }

    /**
     * @param string|null $contentFirstAccountAssignmentInformation
     */
    public function setContentFirstAccountAssignmentInformation(?string $contentFirstAccountAssignmentInformation): void
    {
        $this->contentFirstAccountAssignmentInformation = $contentFirstAccountAssignmentInformation;
    }

    /**
     * @return string|null
     */
    public function getContentSecondAccountAssignmentInformation(): ?string
    {
        return $this->contentSecondAccountAssignmentInformation;
    }

    /**
     * @param string|null $contentSecondAccountAssignmentInformation
     */
    public function setContentSecondAccountAssignmentInformation(?string $contentSecondAccountAssignmentInformation): void
    {
        $this->contentSecondAccountAssignmentInformation = $contentSecondAccountAssignmentInformation;
    }

    /**
     * @return ServiceProvider|null
     */
    public function getPaymentOperator(): ?ServiceProvider
    {
        if (null === $this->paymentOperator && null !== $district = $this->getCommune()->getAdministrativeDistrict()) {
            $this->paymentOperator = $district->getPaymentOperator();
        }
        return $this->paymentOperator;
    }

    /**
     * @param ServiceProvider|null $paymentOperator
     */
    public function setPaymentOperator(?ServiceProvider $paymentOperator): void
    {
        $this->paymentOperator = $paymentOperator;
    }

    /**
     * @param EpaymentService $epaymentService
     * @return self
     */
    public function addEpaymentService(EpaymentService $epaymentService): self
    {
        if (!$this->epaymentServices->contains($epaymentService)) {
            $this->epaymentServices->add($epaymentService);
            $epaymentService->setEpayment($this);
        }

        return $this;
    }

    /**
     * @param EpaymentService $epaymentService
     * @return self
     */
    public function removeEpaymentService($epaymentService): self
    {
        if ($this->epaymentServices->contains($epaymentService)) {
            $this->epaymentServices->removeElement($epaymentService);
        }

        return $this;
    }

    /**
     * @return EpaymentService[]|Collection
     */
    public function getEpaymentServices()
    {
        return $this->epaymentServices;
    }

    /**
     * @param EpaymentService[]|Collection $epaymentServices
     */
    public function setEpaymentServices($epaymentServices): void
    {
        $this->epaymentServices = $epaymentServices;
    }

    /**
     * @return bool
     */
    public function isXFinanceFileRequired(): bool
    {
        return (bool) $this->xFinanceFileRequired;
    }

    /**
     * @param bool|null $xFinanceFileRequired
     */
    public function setXFinanceFileRequired(?bool $xFinanceFileRequired): void
    {
        $this->xFinanceFileRequired = (bool) $xFinanceFileRequired;
    }

    /**
     * @return array|null
     */
    public function getXFinanceFileDays(): ?array
    {
        return $this->xFinanceFileDays;
    }

    /**
     * @param array|null $xFinanceFileDays
     */
    public function setXFinanceFileDays(?array $xFinanceFileDays): void
    {
        $this->xFinanceFileDays = $xFinanceFileDays;
    }

    /**
     * Returns the week day choices used for xFinance
     * @return array<string, string>
     */
    public static function getDayChoices(): array
    {
        $dayChoices = [];
        $f = new \IntlDateFormatter(null, null, null, null, null, 'EEEE');
        for ($i = 0; $i < 7; $i++) {
            $timestamp = strtotime("next monday + $i days");
            $key = strtolower(date('D', $timestamp));
            $dayChoices[$key] = $f->format($timestamp);//strftime('%A', $timestamp)
        }
        return $dayChoices;
    }


    /**
     * Return payment types
     *
     * @return int[]|array
     */
    public function getPaymentTypes(): array
    {
        if (null === $this->paymentTypes) {
            $this->paymentTypes = [];
        }
        return $this->paymentTypes;
    }

    /**
     * Set payment types
     *
     * @param array|int[]|null $paymentTypes
     * @return self
     */
    public function setPaymentTypes(?array $paymentTypes): self
    {
        $this->paymentTypes = $paymentTypes ?: [];

        return $this;
    }

    /**
     * Add a payment type
     *
     * @param int $paymentType
     * @return self
     */
    public function addPaymentType(int $paymentType): self
    {
        if (!in_array($paymentType, $this->getPaymentTypes(), true)) {
            $this->paymentTypes[] = $paymentType;
        }

        return $this;
    }

    /**
     * Remove a payment type
     *
     * @param int $paymentType
     * @return self
     */
    public function removePaymentType(int $paymentType): self
    {
        if (false !== $key = array_search($paymentType, $this->getPaymentTypes(), true)) {
            unset($this->paymentTypes[$key]);
        }

        return $this;
    }
}
