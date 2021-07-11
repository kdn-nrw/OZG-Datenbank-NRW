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

namespace App\Entity\StateGroup;

use App\Entity\AddressTrait;
use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class data center
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_data_center")
 */
class DataCenter extends BaseNamedEntity
{
    use AddressTrait;

    public const OPERATION_TYPE_NONE = 0;
    public const OPERATION_TYPE_OWN = 1;
    public const OPERATION_TYPE_JOINT = 2;

    public static $operationTypeChoices = [
        self::OPERATION_TYPE_OWN => 'app.data_center.entity.operation_type_choices.own',
        self::OPERATION_TYPE_JOINT => 'app.data_center.entity.operation_type_choices.joint',
        self::OPERATION_TYPE_NONE => 'app.data_center.entity.operation_type_choices.none',
    ];

    /**
     * @ORM\Column(type="integer", name="operation_type", nullable=true)
     * @var int|null
     */
    private $operationType;

    /**
     * @var ServiceProvider|null
     * @ORM\OneToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", inversedBy="dataCenter", cascade={"all"})
     * @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $serviceProvider;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider")
     * @ORM\JoinTable(name="ozg_data_center_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="data_center_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     *   }
     * )
     */
    private $otherServiceProviders;

    /**
     * Joint data center info
     *
     * @var string|null
     *
     * @ORM\Column(name="joint_data_center_info", type="text", nullable=true)
     */
    protected $jointDataCenterInfo;

    /**
     * Toggle data_center_waste_heat
     *
     * @var bool
     *
     * @ORM\Column(name="data_center_waste_heat", type="boolean")
     */
    protected $dataCenterWasteHeat = false;

    /**
     * @ORM\Column(type="text", name="data_center_waste_heat_info", nullable=true)
     * @var string|null
     */
    protected $dataCenterWasteHeatInfo;

    /**
     * Toggle data_center_water_cooling
     *
     * @var bool
     *
     * @ORM\Column(name="data_center_water_cooling", type="boolean")
     */
    protected $dataCenterWaterCooling = false;

    /**
     * @ORM\Column(type="text", name="data_center_water_cooling_info", nullable=true)
     * @var string|null
     */
    protected $dataCenterWaterCoolingInfo;

    /**
     * @var DataCenterConsumption[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\StateGroup\DataCenterConsumption", mappedBy="dataCenter", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"year" = "DESC", "id" = "ASC"})
     */
    private $consumptions;

    public function __construct()
    {
        $this->otherServiceProviders = new ArrayCollection();
        $this->consumptions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getOperationType(): int
    {
        return (int) $this->operationType;
    }

    /**
     * @param int|null $operationType
     */
    public function setOperationType(?int $operationType): void
    {
        $this->operationType = $operationType;
    }

    /**
     * Returns the label key for the current data type
     *
     * @return string
     */
    public function getOperationTypeLabel(): string
    {
        return self::$operationTypeChoices[$this->getOperationType()];
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider|null $serviceProvider
     */
    public function setServiceProvider(?ServiceProvider $serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
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
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return bool
     */
    public function isDataCenterWasteHeat(): bool
    {
        return $this->dataCenterWasteHeat;
    }

    /**
     * @param bool $dataCenterWasteHeat
     */
    public function setDataCenterWasteHeat(bool $dataCenterWasteHeat): void
    {
        $this->dataCenterWasteHeat = $dataCenterWasteHeat;
    }

    /**
     * @return string|null
     */
    public function getDataCenterWasteHeatInfo(): ?string
    {
        return $this->dataCenterWasteHeatInfo;
    }

    /**
     * @param string|null $dataCenterWasteHeatInfo
     */
    public function setDataCenterWasteHeatInfo(?string $dataCenterWasteHeatInfo): void
    {
        $this->dataCenterWasteHeatInfo = $dataCenterWasteHeatInfo;
    }

    /**
     * @return bool
     */
    public function isDataCenterWaterCooling(): bool
    {
        return $this->dataCenterWaterCooling;
    }

    /**
     * @param bool $dataCenterWaterCooling
     */
    public function setDataCenterWaterCooling(bool $dataCenterWaterCooling): void
    {
        $this->dataCenterWaterCooling = $dataCenterWaterCooling;
    }

    /**
     * @return string|null
     */
    public function getDataCenterWaterCoolingInfo(): ?string
    {
        return $this->dataCenterWaterCoolingInfo;
    }

    /**
     * @param string|null $dataCenterWaterCoolingInfo
     */
    public function setDataCenterWaterCoolingInfo(?string $dataCenterWaterCoolingInfo): void
    {
        $this->dataCenterWaterCoolingInfo = $dataCenterWaterCoolingInfo;
    }

    /**
     * @param ServiceProvider $otherServiceProvider
     * @return self
     */
    public function addOtherServiceProvider(ServiceProvider $otherServiceProvider): self
    {
        if (!$this->otherServiceProviders->contains($otherServiceProvider)) {
            $this->otherServiceProviders->add($otherServiceProvider);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $otherServiceProvider
     * @return self
     */
    public function removeOtherServiceProvider(ServiceProvider $otherServiceProvider): self
    {
        if ($this->otherServiceProviders->contains($otherServiceProvider)) {
            $this->otherServiceProviders->removeElement($otherServiceProvider);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getOtherServiceProviders(): Collection
    {
        return $this->otherServiceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $otherServiceProviders
     */
    public function setOtherServiceProviders($otherServiceProviders): void
    {
        $this->otherServiceProviders = $otherServiceProviders;
    }

    /**
     * @return string|null
     */
    public function getJointDataCenterInfo(): ?string
    {
        return $this->jointDataCenterInfo;
    }

    /**
     * @param string|null $jointDataCenterInfo
     */
    public function setJointDataCenterInfo(?string $jointDataCenterInfo): void
    {
        $this->jointDataCenterInfo = $jointDataCenterInfo;
    }

    /**
     * @param DataCenterConsumption $consumption
     * @return self
     */
    public function addConsumption(DataCenterConsumption $consumption): self
    {
        if (!$this->consumptions->contains($consumption)) {
            $this->consumptions->add($consumption);
            $consumption->setDataCenter($this);
        }

        return $this;
    }

    /**
     * @param DataCenterConsumption $consumption
     * @return self
     */
    public function removeConsumption(DataCenterConsumption $consumption): self
    {
        if ($this->consumptions->contains($consumption)) {
            $this->consumptions->removeElement($consumption);
        }

        return $this;
    }

    /**
     * @return DataCenterConsumption[]|Collection
     */
    public function getConsumptions()
    {
        return $this->consumptions;
    }

    /**
     * @param DataCenterConsumption[]|Collection $consumptions
     */
    public function setConsumptions($consumptions): void
    {
        $this->consumptions = $consumptions;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->name && null !== $this->serviceProvider) {
            return $this->serviceProvider . '';
        }
        return $this->getName() ?? 'NULL';
    }
}
