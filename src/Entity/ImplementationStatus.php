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
use Doctrine\ORM\Mapping as ORM;


/**
 * Class implementation status
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation_status")
 * @ORM\HasLifecycleCallbacks
 */
class ImplementationStatus extends BaseNamedEntity
{
    public const STATUS_ID_PREPARED = 2;
    public const STATUS_ID_CONCEPT = 3;
    public const STATUS_ID_IMPLEMENTATION = 4;
    public const STATUS_ID_COMMISSIONING = 6;

    public const STATUS_SWITCH_PREPARED = 1;
    public const STATUS_SWITCH_CONCEPT = 2;
    public const STATUS_SWITCH_IMPLEMENTATION = 3;
    public const STATUS_SWITCH_COMMISSIONING = 4;

    public static $statusSwitchChoices = [
        self::STATUS_SWITCH_PREPARED => 'app.implementation_status.entity.status_switch_choices.project_start_at',
        self::STATUS_SWITCH_CONCEPT => 'app.implementation_status.entity.status_switch_choices.concept_status_at',
        self::STATUS_SWITCH_IMPLEMENTATION => 'app.implementation_status.entity.status_switch_choices.implementation_status_at',
        self::STATUS_SWITCH_COMMISSIONING => 'app.implementation_status.entity.status_switch_choices.commissioning_status_at',
    ];

    /**
     * statuslv
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 0;

    /**
     * statuslverkl
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Flag indicates if status is set automatically or manually
     *
     * @var bool|null
     *
     * @ORM\Column(name="set_automatically", type="boolean", nullable=true)
     */
    protected $setAutomatically = false;

    /**
     * @ORM\Column(type="integer", name="status_switch", nullable=true)
     * @var int|null
     */
    private $statusSwitch;

    /**
     * Previous status
     * @var ImplementationStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ImplementationStatus")
     * @ORM\JoinColumn(name="prev_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $prevStatus;

    /**
     * Next status
     * @var ImplementationStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ImplementationStatus")
     * @ORM\JoinColumn(name="next_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $nextStatus;

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
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
     * @return bool
     */
    public function isSetAutomatically(): bool
    {
        return (bool) $this->setAutomatically;
    }

    /**
     * @param bool $setAutomatically
     */
    public function setSetAutomatically(bool $setAutomatically): void
    {
        $this->setAutomatically = $setAutomatically;
    }

    /**
     * @return int|null
     */
    public function getStatusSwitch(): ?int
    {
        return $this->statusSwitch;
    }

    /**
     * @param int|null $statusSwitch
     */
    public function setStatusSwitch(?int $statusSwitch): void
    {
        $this->statusSwitch = $statusSwitch;
    }

    /**
     * @return ImplementationStatus|null
     */
    public function getPrevStatus(): ?ImplementationStatus
    {
        return $this->prevStatus;
    }

    /**
     * @param ImplementationStatus|null $prevStatus
     */
    public function setPrevStatus(?ImplementationStatus $prevStatus): void
    {
        $this->prevStatus = $prevStatus;
    }

    /**
     * @return ImplementationStatus|null
     */
    public function getNextStatus(): ?ImplementationStatus
    {
        return $this->nextStatus;
    }

    /**
     * @param ImplementationStatus|null $nextStatus
     */
    public function setNextStatus(?ImplementationStatus $nextStatus): void
    {
        $this->nextStatus = $nextStatus;
    }

}
