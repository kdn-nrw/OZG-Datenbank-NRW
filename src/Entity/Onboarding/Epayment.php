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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ePayBL
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_epayment")
 */
class Epayment extends AbstractOnboardingEntity
{
    public const DEFAULT_PAYMENT_PROVIDER = 'Girosolution';

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
    private $paymentUser;

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
     * @ORM\Column(type="string", name="length_receipt_number", length=255, nullable=true)
     * @var string|null
     */
    protected $lengthReceiptNumber;

    /**
     * @ORM\Column(type="boolean", name="cash_register_check_procedure_status", nullable=true)
     * @var boolean
     */
    protected $cashRegisterCheckProcedureStatus = true;

    /**
     * @ORM\Column(type="string", name="length_first_account_assignment_information", length=255, nullable=true)
     * @var string|null
     */
    protected $lengthFirstAccountAssignmentInformation;

    /**
     * @ORM\Column(type="string", name="length_second_account_assignment_information", length=255, nullable=true)
     * @var string|null
     */
    protected $lengthSecondAccountAssignmentInformation;

    public function __construct(Commune $commune)
    {
        parent::__construct($commune);
        $this->projects = new ArrayCollection();
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
     * @return string|null
     */
    public function getLengthReceiptNumber(): ?string
    {
        return $this->lengthReceiptNumber;
    }

    /**
     * @param string|null $lengthReceiptNumber
     */
    public function setLengthReceiptNumber(?string $lengthReceiptNumber): void
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
     * @return string|null
     */
    public function getLengthFirstAccountAssignmentInformation(): ?string
    {
        return $this->lengthFirstAccountAssignmentInformation;
    }

    /**
     * @param string|null $lengthFirstAccountAssignmentInformation
     */
    public function setLengthFirstAccountAssignmentInformation(?string $lengthFirstAccountAssignmentInformation): void
    {
        $this->lengthFirstAccountAssignmentInformation = $lengthFirstAccountAssignmentInformation;
    }

    /**
     * @return string|null
     */
    public function getLengthSecondAccountAssignmentInformation(): ?string
    {
        return $this->lengthSecondAccountAssignmentInformation;
    }

    /**
     * @param string|null $lengthSecondAccountAssignmentInformation
     */
    public function setLengthSecondAccountAssignmentInformation(?string $lengthSecondAccountAssignmentInformation): void
    {
        $this->lengthSecondAccountAssignmentInformation = $lengthSecondAccountAssignmentInformation;
    }

    protected function getRequiredPropertiesForCompletion(): array
    {
        return [
            'paymentProviderAccountId', 'paymentUser', 'mandatorEmail',
            //'testIpAddress',
            'street', 'zipCode', 'town',
            'projects',
            'clientNumberIntegration', 'clientNumberProduction', 'managerNumber', 'budgetOffice', 'objectNumber',
            'cashRegisterPersonalAccountNumber', 'indicatorDunningProcedure', 'bookingText', 'descriptionOfTheBookingList',
            'managerNo', 'applicationName', 'lengthReceiptNumber', 'cashRegisterCheckProcedureStatus',
            'lengthFirstAccountAssignmentInformation', 'lengthSecondAccountAssignmentInformation',
        ];
    }
}
