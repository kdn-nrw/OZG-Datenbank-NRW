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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class MinistryState
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_ministry_state")
 */
class MinistryState extends BaseNamedEntity implements OrganisationEntityInterface
{
    use AddressTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="Organisation", inversedBy="ministryState", cascade={"all"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $organisation;

    /**
     * Short name
     * @var string|null
     *
     * @ORM\Column(type="string", name="short_name", length=255, nullable=true)
     */
    private $shortName;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceSystem", inversedBy="stateMinistries")
     * @ORM\JoinTable(name="ozg_ministry_state_service_system",
     *     joinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceSystems;

    /**
     * @var Service[]|Collection
     * @ORM\ManyToMany(targetEntity="Service", mappedBy="authorityStateMinistries")
     */
    private $serviceAuthorities;

    public function __construct()
    {
        $this->serviceSystems = new ArrayCollection();
        $this->serviceAuthorities = new ArrayCollection();
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setMinistryState($this);
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string|null $shortName
     */
    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem): self
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addStateMinistry($this);
        }

        return $this;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function removeServiceSystem($serviceSystem): self
    {
        if ($this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->removeElement($serviceSystem);
            $serviceSystem->removeStateMinistry($this);
        }

        return $this;
    }

    /**
     * @return ServiceSystem[]|Collection
     */
    public function getServiceSystems()
    {
        return $this->serviceSystems;
    }

    /**
     * @param ServiceSystem[]|Collection $serviceSystems
     */
    public function setServiceSystems($serviceSystems): void
    {
        $this->serviceSystems = $serviceSystems;
    }

    /**
     * @param Service $serviceAuthority
     * @return self
     */
    public function addServiceAuthority($serviceAuthority): self
    {
        if (!$this->serviceAuthorities->contains($serviceAuthority)) {
            $this->serviceAuthorities->add($serviceAuthority);
            $serviceAuthority->addStateMinistry($this);
        }

        return $this;
    }

    /**
     * @param Service $serviceAuthority
     * @return self
     */
    public function removeServiceAuthority($serviceAuthority): self
    {
        if ($this->serviceAuthorities->contains($serviceAuthority)) {
            $this->serviceAuthorities->removeElement($serviceAuthority);
            $serviceAuthority->removeStateMinistry($this);
        }

        return $this;
    }

    /**
     * @return Service[]|Collection
     */
    public function getServiceAuthorities()
    {
        return $this->serviceAuthorities;
    }

    /**
     * @param Service[]|Collection $serviceAuthorities
     */
    public function setServiceAuthorities($serviceAuthorities): void
    {
        $this->serviceAuthorities = $serviceAuthorities;
    }

}
