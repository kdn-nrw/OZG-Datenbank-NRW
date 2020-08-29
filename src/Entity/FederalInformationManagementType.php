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

use App\Entity\Base\BaseEntity;
use App\Entity\FederalInformationManagementType as FederalInformationManagementEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class FederalInformationManagementType
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_fim")
 */
class FederalInformationManagementType extends BaseEntity
{
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_SUBMITTED = 2;
    public const STATUS_APPROVED = 3;

    public const TYPE_DESCRIPTION = 'description';
    public const TYPE_MASTER_DATA_FIELDS = 'master_data_fields';
    public const TYPE_ROOT_PROCESS = 'root_process';
    public const TYPE_REFERENCE_DATA_FIELDS = 'reference_data_fields';
    public const TYPE_REFERENCE_PROCESS = 'reference_process';

    public static $mapTypes = [
        FederalInformationManagementEntity::TYPE_DESCRIPTION             => 'app.service.fim.type_choices.description',
        FederalInformationManagementEntity::TYPE_MASTER_DATA_FIELDS      => 'app.service.fim.type_choices.master_data_fields',
        FederalInformationManagementEntity::TYPE_ROOT_PROCESS            => 'app.service.fim.type_choices.root_process',
        FederalInformationManagementEntity::TYPE_REFERENCE_DATA_FIELDS   => 'app.service.fim.type_choices.reference_data_fields',
        FederalInformationManagementEntity::TYPE_REFERENCE_PROCESS       => 'app.service.fim.type_choices.reference_process',
    ];

    public static $statusChoices = [
        FederalInformationManagementEntity::STATUS_IN_PROGRESS  => 'app.service.fim.entity.status_choices.in_progress',
        FederalInformationManagementEntity::STATUS_SUBMITTED    => 'app.service.fim.entity.status_choices.submitted',
        FederalInformationManagementEntity::STATUS_APPROVED     => 'app.service.fim.entity.status_choices.approved',
    ];

    /**
     * @var Service|null
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="fimTypes", cascade={"persist"})
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
     * Returns the name of this contact
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDataType() . ' ('.$this->getStatus().')';
    }
}
