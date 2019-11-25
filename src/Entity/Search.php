<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class Search
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_search")
 * @ORM\HasLifecycleCallbacks
 */
class Search extends BaseEntity
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="admin_id", type="string", length=255, nullable=true)
     */
    private $adminId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $route;

    /**
     * Filter parameters
     * @var array|null
     *
     * @ORM\Column(name="parameters", type="json", nullable=true)
     */
    private $parameters = [];

    /**
     * Filter query string
     * @var string|null
     *
     * @ORM\Column(name="query_string", type="text", nullable=true)
     */
    private $queryString = '';

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="show_for_all", type="boolean")
     */
    protected $showForAll = false;

    /**
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @return string|null
     */
    public function getAdminId(): ?string
    {
        return $this->adminId;
    }

    /**
     * @param string|null $adminId
     */
    public function setAdminId(?string $adminId): void
    {
        $this->adminId = $adminId;
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
     * @return array|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @param array|null $parameters
     */
    public function setParameters(?array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Update the parameters from the query string
     */
    public function updateParameters()
    {
        $queryString = $this->getQueryString();
        if (!empty($queryString)) {
            parse_str(rawurldecode($queryString), $queryParams);
            $this->setParameters($queryParams);
        }
    }

    /**
     * @return string|null
     */
    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    /**
     * @param string|null $queryString
     */
    public function setQueryString(?string $queryString): void
    {
        $this->queryString = $queryString;
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
    public function isShowForAll(): bool
    {
        return $this->showForAll;
    }

    /**
     * @param bool $showForAll
     */
    public function setShowForAll(bool $showForAll): void
    {
        $this->showForAll = $showForAll;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


}
