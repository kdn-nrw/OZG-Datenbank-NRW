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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class LogSummary
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_statistics_log_path_info",uniqueConstraints={@ORM\UniqueConstraint(name="route_idx", columns={"route"})},indexes={@ORM\Index(name="IDX_PATH_TYPE", columns={"path_type"})})
 */
class LogPathInfo extends BaseEntity
{
    public const PATH_TYPE_FRONTEND = 1;
    public const PATH_TYPE_BACKEND = 2;
    public const PATH_TYPE_API = 3;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=1024, nullable=false)
     */
    private $path;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * Type if the path
     *
     * @var int
     *
     * @ORM\Column(name="path_type", type="integer", nullable=false)
     */
    protected $pathType = self::PATH_TYPE_BACKEND;

    /**
     * @var LogSummary[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Statistics\LogSummary", mappedBy="pathInfo", cascade={"persist"})
     */
    private $summaryItems;

    /**
     * @var LogSearch[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Statistics\LogSearch", mappedBy="pathInfo", cascade={"persist"})
     */
    private $searchItems;

    public function __construct()
    {
        $this->summaryItems = new ArrayCollection();
        $this->searchItems = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string|null $route
     */
    public function setRoute(?string $route): void
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }


    /**
     * @param LogSummary $summaryItem
     * @return self
     */
    public function addLogSummaryItem(LogSummary $summaryItem): self
    {
        if (!$this->summaryItems->contains($summaryItem)) {
            $this->summaryItems->add($summaryItem);
            $summaryItem->setPathInfo($this);
        }

        return $this;
    }

    /**
     * @param LogSummary $summaryItem
     * @return self
     */
    public function removeLogSummaryItem(LogSummary $summaryItem): self
    {
        if ($this->summaryItems->contains($summaryItem)) {
            $this->summaryItems->removeElement($summaryItem);
            $summaryItem->setPathInfo(null);
        }

        return $this;
    }

    /**
     * @return LogSummary[]|Collection
     */
    public function getLogSummaryItems()
    {
        return $this->summaryItems;
    }

    /**
     * @param LogSummary[]|Collection $summaryItems
     */
    public function setLogSummaryItem($summaryItems): void
    {
        $this->summaryItems = $summaryItems;
    }


    /**
     * @param LogSearch $searchItem
     * @return self
     */
    public function addLogSearchItem(LogSearch $searchItem): self
    {
        if (!$this->searchItems->contains($searchItem)) {
            $this->searchItems->add($searchItem);
            $searchItem->setPathInfo($this);
        }

        return $this;
    }

    /**
     * @param LogSearch $searchItem
     * @return self
     */
    public function removeLogSearchItem(LogSearch $searchItem): self
    {
        if ($this->searchItems->contains($searchItem)) {
            $this->searchItems->removeElement($searchItem);
            $searchItem->setPathInfo(null);
        }

        return $this;
    }

    /**
     * @return LogSearch[]|Collection
     */
    public function getLogSearchItems()
    {
        return $this->searchItems;
    }

    /**
     * @param LogSearch[]|Collection $searchItems
     */
    public function setLogSearchItem($searchItems): void
    {
        $this->searchItems = $searchItems;
    }

}
