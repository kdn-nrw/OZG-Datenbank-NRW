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
 * Class LogSearch
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_statistics_log_search",indexes={@ORM\Index(name="IDX_PATH_SEARCH", columns={"path_info_id", "search_term"})})
 */
class LogSearch extends BaseEntity
{

    /**
     * @var LogPathInfo|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Statistics\LogPathInfo", inversedBy="searchItems", cascade={"persist"})
     * @ORM\JoinColumn(name="path_info_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $pathInfo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="search_term", type="string", length=255, nullable=true)
     */
    protected $searchTerm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="search_count", type="integer", nullable=true)
     */
    protected $searchCount;

    /**
     * @return string|null
     */
    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    /**
     * @param string|null $searchTerm
     */
    public function setSearchTerm(?string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return int|null
     */
    public function getSearchCount(): ?int
    {
        return $this->searchCount;
    }

    /**
     * @param int|null $searchCount
     */
    public function setSearchCount(?int $searchCount): void
    {
        $this->searchCount = $searchCount;
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
