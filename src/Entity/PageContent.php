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
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class PageContent
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_page_content")
 * @ApiResource
 */
class PageContent extends BaseEntity
{
    public const PAGE_HOME = 1;
    public const PAGE_SERVICE_SYSTEMS = 2;
    public const PAGE_SERVICES = 3;
    public const PAGE_IMPLEMENTATION_PROJECTS = 4;
    public const PAGE_SOLUTIONS = 5;
    public const PAGE_COMMUNES = 6;

    public static $pageChoices = [
        self::PAGE_HOME => 'app.page_content.entity.page_choices.home',
        self::PAGE_SERVICE_SYSTEMS => 'app.page_content.entity.page_choices.service_systems',
        self::PAGE_SERVICES => 'app.page_content.entity.page_choices.services',
        self::PAGE_IMPLEMENTATION_PROJECTS => 'app.page_content.entity.page_choices.implementation_projects',
        self::PAGE_SOLUTIONS => 'app.page_content.entity.page_choices.solutions',
        self::PAGE_COMMUNES => 'app.page_content.entity.page_choices.communes',
    ];

    /**
     * @ORM\Column(type="integer", name="page")
     * @var int
     */
    private $page = self::PAGE_HOME;

    /**
     * @ORM\Column(type="integer", name="position", nullable=true)
     * @var int
     */
    private $position = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $headline;

    /**
     * Page content body text
     *
     * @var string|null
     *
     * @ORM\Column(name="bodytext", type="text", nullable=true)
     */
    private $bodytext = '';

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return (int) $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = (int) $position;
    }

    /**
     * @return string|null
     */
    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    /**
     * @param string|null $headline
     */
    public function setHeadline(?string $headline): void
    {
        $this->headline = $headline;
    }

    /**
     * @return string|null
     */
    public function getBodytext(): ?string
    {
        return $this->bodytext;
    }

    /**
     * @param string|null $bodytext
     */
    public function setBodytext(?string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = (string) $this->getHeadline();
        if (null === $name) {
            return 'NULL';
        }
        return $name;
    }

}
