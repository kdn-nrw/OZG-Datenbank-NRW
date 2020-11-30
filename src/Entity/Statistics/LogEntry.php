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

namespace App\Entity\Statistics;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class LogEntry
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_statistics_log_entry")
 */
class LogEntry extends BaseEntity
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="request_method", type="string", length=255, nullable=true)
     */
    private $requestMethod;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_locale", type="string", length=255, nullable=true)
     */
    private $requestLocale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path_info", type="string", length=1024, nullable=true)
     */
    private $pathInfo;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * Request parameters (route, admin etc.)
     * @var array|null
     *
     * @ORM\Column(name="request_attributes", type="json", nullable=true)
     */
    private $requestAttributes = [];

    /**
     * Request query parameters
     * @var array|null
     *
     * @ORM\Column(name="query_parameters", type="json", nullable=true)
     */
    private $queryParameters = [];

    /**
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titlePrefix;

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
     * @return string|null
     */
    public function getRequestMethod(): ?string
    {
        return $this->requestMethod;
    }

    /**
     * @param string|null $requestMethod
     */
    public function setRequestMethod(?string $requestMethod): void
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return string|null
     */
    public function getRequestLocale(): ?string
    {
        return $this->requestLocale;
    }

    /**
     * @param string|null $requestLocale
     */
    public function setRequestLocale(?string $requestLocale): void
    {
        $this->requestLocale = $requestLocale;
    }

    /**
     * @return string|null
     */
    public function getPathInfo(): ?string
    {
        return $this->pathInfo;
    }

    /**
     * @param string|null $pathInfo
     */
    public function setPathInfo(?string $pathInfo): void
    {
        $this->pathInfo = $pathInfo;
    }

    /**
     * @return array|null
     */
    public function getRequestAttributes(): ?array
    {
        return $this->requestAttributes;
    }

    /**
     * @param array|null $requestAttributes
     */
    public function setRequestAttributes(?array $requestAttributes): void
    {
        $this->requestAttributes = $requestAttributes;
    }

    /**
     * @return array|null
     */
    public function getQueryParameters(): ?array
    {
        return $this->queryParameters;
    }

    /**
     * @param array|null $queryParameters
     */
    public function setQueryParameters(?array $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return self
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getTitlePrefix(): ?string
    {
        return $this->titlePrefix;
    }

    /**
     * @param string|null $titlePrefix
     */
    public function setTitlePrefix(?string $titlePrefix): void
    {
        $this->titlePrefix = $titlePrefix;
    }

}
