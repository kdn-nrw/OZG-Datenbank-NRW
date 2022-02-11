<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Statistics;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class LogSummary
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_statistics_log_summary")
 */
class LogSummary extends BaseEntity
{

    /**
     * @var LogPathInfo|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Statistics\LogPathInfo", inversedBy="summaryItems", cascade={"persist"})
     * @ORM\JoinColumn(name="path_info_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $pathInfo;

    /**
     *
     * @var string|null
     *
     * @ORM\Column(name="entry_date", type="text", nullable=true)
     */
    protected $entryDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="access_count", type="integer", nullable=true)
     */
    protected $accessCount;

    /**
     * @return string|null
     */
    public function getEntryDate(): ?string
    {
        return $this->entryDate;
    }

    /**
     * @param string|null $entryDate
     */
    public function setEntryDate(?string $entryDate): void
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return int|null
     */
    public function getAccessCount(): ?int
    {
        return $this->accessCount;
    }

    /**
     * @param int|null $accessCount
     */
    public function setAccessCount(?int $accessCount): void
    {
        $this->accessCount = $accessCount;
    }

    /**
     * @return LogPathInfo|null
     */
    public function getPathInfo(): ?LogPathInfo
    {
        return $this->pathInfo;
    }

    /**
     * @param LogPathInfo|null $pathInfo
     */
    public function setPathInfo(?LogPathInfo $pathInfo): void
    {
        $this->pathInfo = $pathInfo;
    }

}
