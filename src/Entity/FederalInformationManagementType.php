<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Api\ServiceBaseResult;
use App\Entity\Base\BaseEntity;
use App\Entity\Base\CustomEntityLabelInterface;
use App\Entity\FederalInformationManagementType as FederalInformationManagementEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class FederalInformationManagementType
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_fim")
 */
class FederalInformationManagementType extends BaseEntity implements CustomEntityLabelInterface
{
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_SUBMITTED = 2;
    public const STATUS_APPROVED = 3;
    public const STATUS_NOT_PROVIDED = 4;

    public const TYPE_DESCRIPTION = 'description';
    public const TYPE_MASTER_DATA_FIELDS = 'master_data_fields';
    public const TYPE_ROOT_PROCESS = 'root_process';
    public const TYPE_REFERENCE_DATA_FIELDS = 'reference_data_fields';
    public const TYPE_REFERENCE_PROCESS = 'reference_process';

    public static $mapTypes = [
        FederalInformationManagementEntity::TYPE_DESCRIPTION => 'app.federal_information_management_type.entity.type_choices.description',
        FederalInformationManagementEntity::TYPE_MASTER_DATA_FIELDS => 'app.federal_information_management_type.entity.type_choices.master_data_fields',
        FederalInformationManagementEntity::TYPE_ROOT_PROCESS => 'app.federal_information_management_type.entity.type_choices.root_process',
        FederalInformationManagementEntity::TYPE_REFERENCE_DATA_FIELDS => 'app.federal_information_management_type.entity.type_choices.reference_data_fields',
        FederalInformationManagementEntity::TYPE_REFERENCE_PROCESS => 'app.federal_information_management_type.entity.type_choices.reference_process',
    ];

    public static $statusChoices = [
        FederalInformationManagementEntity::STATUS_IN_PROGRESS => 'app.federal_information_management_type.entity.status_choices.in_progress',
        FederalInformationManagementEntity::STATUS_SUBMITTED => 'app.federal_information_management_type.entity.status_choices.submitted',
        FederalInformationManagementEntity::STATUS_APPROVED => 'app.federal_information_management_type.entity.status_choices.approved',
        FederalInformationManagementEntity::STATUS_NOT_PROVIDED => 'app.federal_information_management_type.entity.status_choices.not_provided',
    ];

    /**
     * @var Service|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="fimTypes", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $service;

    /**
     * @ORM\Column(type="integer", name="status")
     * @var int
     */
    private $status = self::STATUS_IN_PROGRESS;

    /**
     * The FIM data type
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $dataType;

    /**
     * Notes
     *
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes = '';

    /**
     * @var ServiceBaseResult|null
     * @ORM\OneToOne(targetEntity="App\Entity\Api\ServiceBaseResult", mappedBy="fimType", cascade={"all"})
     * @ORM\JoinColumn(name="service_base_result_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $serviceBaseResult;

    /**
     * Implementation team proposal
     *
     * @var string|null
     *
     * @ORM\Column(name="implementation_team_proposal", type="text", nullable=true)
     */
    private $implementationTeamProposal;

    /**
     * @return Service|null
     */
    public function getService(): ?Service
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = (int)$status;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @param string $dataType
     */
    public function setDataType(string $dataType): void
    {
        $this->dataType = $dataType;
    }

    /**
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * Returns the label key for the current data type
     *
     * @return string
     */
    public function getDataTypeLabel(): string
    {
        return self::$mapTypes[$this->getDataType()];
    }

    /**
     * Returns the label key for the current status
     *
     * @return string
     */
    public function getStatusLabel(): string
    {
        return self::$statusChoices[$this->getStatus()];
    }

    /**
     * @return ServiceBaseResult|null
     */
    public function getServiceBaseResult(): ?ServiceBaseResult
    {
        return $this->serviceBaseResult;
    }

    /**
     * @param ServiceBaseResult|null $serviceBaseResult
     */
    public function setServiceBaseResult(?ServiceBaseResult $serviceBaseResult): void
    {
        $this->serviceBaseResult = $serviceBaseResult;
    }

    /**
     * @return string|null
     */
    public function getImplementationTeamProposal(): ?string
    {
        return $this->implementationTeamProposal;
    }

    /**
     * @param string|null $implementationTeamProposal
     */
    public function setImplementationTeamProposal(?string $implementationTeamProposal): void
    {
        $this->implementationTeamProposal = $implementationTeamProposal;
    }

    /**
     * @return string|null
     */
    public function getLabelKey(): ?string
    {
        return $this->getDataTypeLabel();
    }

    /**
     * Returns the name of this contact
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDataType() . ' (' . $this->getStatus() . ')';
    }
}
