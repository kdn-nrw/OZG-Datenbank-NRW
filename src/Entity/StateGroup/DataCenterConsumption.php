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

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class data center energy
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_data_center_consumption")
 */
class DataCenterConsumption extends BaseNamedEntity
{
    /**
     * @var DataCenter|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\DataCenter", inversedBy="consumptions")
     * @ORM\JoinColumn(name="data_center_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $dataCenter;

    /**
     * @var int|null
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var int|null
     *
     * @ORM\Column(name="power_consumption", type="integer", nullable=true)
     */
    protected $powerConsumption;

    /**
     * Comment
     *
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    protected $comment = '';

    /**
     * @return DataCenter|null
     */
    public function getDataCenter(): ?DataCenter
    {
        return $this->dataCenter;
    }

    /**
     * @param DataCenter|null $dataCenter
     */
    public function setDataCenter(?DataCenter $dataCenter): void
    {
        $this->dataCenter = $dataCenter;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return int|null
     */
    public function getPowerConsumption(): ?int
    {
        return $this->powerConsumption;
    }

    /**
     * @param int|null $powerConsumption
     */
    public function setPowerConsumption(?int $powerConsumption): void
    {
        $this->powerConsumption = $powerConsumption;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

}
