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

namespace App\Entity\StateGroup;

use App\Entity\AddressTrait;
use App\Entity\Base\AppBaseEntity;
use App\Entity\Base\SluggableEntityTrait;
use App\Entity\Base\SluggableInterface;
use App\Entity\Base\SoftdeletableEntityInterface;
use App\Entity\ContactTextTrait;
use App\Entity\HasManufacturerEntityInterface;
use App\Entity\Laboratory;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use App\Entity\OrganisationTrait;
use App\Entity\Portal;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\UrlTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Class Commune
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\CommuneRepository")
 * @ORM\Table(name="ozg_commune")
 */
class Commune extends AppBaseEntity implements OrganisationEntityInterface, HasManufacturerEntityInterface, SluggableInterface, HasMetaDateEntityInterface
{
    use AddressTrait;
    use ContactTextTrait;
    use OrganisationTrait;
    use SluggableEntityTrait;
    use UrlTrait;

    public const TYPE_CITY_REGION = 1;
    public const TYPE_CONSTITUENCY = 2;
    public const TYPE_MUNICIPALITY_DISTRICT = 3;
    public const TYPE_LARGE_CITY_DISTRICT = 4;
    public const TYPE_INDEPENDENT_CITY = 5;
    public const TYPE_MIDDLE_CITY_DISTRICT = 6;
    public const TYPE_CITY_DISTRICT = 7;

    /**
     * @var string|null
     * @Gedmo\Slug(fields={"name", "id"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="App\Entity\Organisation", inversedBy="commune", cascade={"all"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $organisation;

    /**
     * @var Office[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\StateGroup\Office", mappedBy="commune", cascade={"all"}, orphanRemoval=true)
     * @deprecated
     */
    private $offices;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider", inversedBy="communes")
     * @ORM\JoinTable(name="ozg_communes_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", mappedBy="communes")
     */
    private $solutions;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure", mappedBy="communes")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $specializedProcedures;

    /**
     * @var Portal[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Portal", inversedBy="communes")
     * @ORM\JoinTable(name="ozg_commune_portals",
     *     joinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="portal_id", referencedColumnName="id")
     *   }
     * )
     */
    private $portals;

    /**
     * @var CentralAssociation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\CentralAssociation", inversedBy="communes")
     * @ORM\JoinTable(name="ozg_commune_central_association",
     *     joinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="central_association_id", referencedColumnName="id")
     *   }
     * )
     */
    private $centralAssociations;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Laboratory")
     * @ORM\JoinTable(name="ozg_commune_laboratory",
     *     joinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="laboratory_id", referencedColumnName="id")
     *   }
     * )
     */
    private $laboratories;

    /**
     * @var Commune|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", cascade={"persist"})
     * @ORM\JoinColumn(name="constituency_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $constituency;

    /**
     * Administrative district
     * @var AdministrativeDistrict|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\AdministrativeDistrict", inversedBy="communes", cascade={"persist"})
     * @ORM\JoinColumn(name="administrative_district_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $administrativeDistrict;

    /**
     * Commune type
     * @var CommuneType|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\CommuneType", inversedBy="communes")
     * @ORM\JoinColumn(name="commune_type_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $communeType;

    /**
     * Main commune email
     * @var string|null
     *
     * @ORM\Column(type="string", name="main_email", length=255, nullable=true)
     */
    private $mainEmail;

    /**
     * Official community_key
     * @var string|null
     *
     * @ORM\Column(type="string", name="official_community_key", length=255, nullable=true)
     */
    private $officialCommunityKey;

    public function __construct()
    {
        $this->centralAssociations = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setCommune($this);
        $this->organisation->setFromReference($this);
    }

    /**
     * @param Office $office
     * @return self
     * @deprecated
     */
    public function addOffice($office): self
    {
        if (!$this->offices->contains($office)) {
            $this->offices->add($office);
            $office->setCommune($this);
        }

        return $this;
    }

    /**
     * @param Office $office
     * @return self
     * @deprecated
     */
    public function removeOffice($office): self
    {
        if ($this->offices->contains($office)) {
            $this->offices->removeElement($office);
            if ($office instanceof SoftdeletableEntityInterface) {
                $office->setDeletedAt(new DateTime());
            }
        }

        return $this;
    }

    /**
     * @return Office[]|Collection
     * @deprecated
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * @param Office[]|Collection $offices
     * @deprecated
     */
    public function setOffices($offices): void
    {
        $this->offices = $offices;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
            $serviceProvider->addCommune($this);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider($serviceProvider): self
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
            $serviceProvider->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $serviceProviders
     */
    public function setServiceProviders($serviceProviders): void
    {
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addCommune($this);
        }

        return $this;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function removeSolution($solution): self
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * @param Solution[]|Collection $solutions
     */
    public function setSolutions($solutions): void
    {
        $this->solutions = $solutions;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addCommune($this);
        }

        return $this;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function removeSpecializedProcedure($specializedProcedure): self
    {
        if ($this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->removeElement($specializedProcedure);
            $specializedProcedure->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return SpecializedProcedure[]|Collection
     */
    public function getSpecializedProcedures()
    {
        return $this->specializedProcedures;
    }

    /**
     * @param SpecializedProcedure[]|Collection $specializedProcedures
     */
    public function setSpecializedProcedures($specializedProcedures): void
    {
        $this->specializedProcedures = $specializedProcedures;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function addPortal($portal): self
    {
        if (!$this->portals->contains($portal)) {
            $this->portals->add($portal);
            $portal->addCommune($this);
        }

        return $this;
    }

    /**
     * @param Portal $portal
     * @return self
     */
    public function removePortal($portal): self
    {
        if ($this->portals->contains($portal)) {
            $this->portals->removeElement($portal);
            $portal->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return Portal[]|Collection
     */
    public function getPortals()
    {
        return $this->portals;
    }

    /**
     * @param Portal[]|Collection $portals
     */
    public function setPortals($portals): void
    {
        $this->portals = $portals;
    }

    /**
     * @param CentralAssociation $centralAssociation
     * @return self
     */
    public function addCentralAssociation($centralAssociation): self
    {
        if (!$this->centralAssociations->contains($centralAssociation)) {
            $this->centralAssociations->add($centralAssociation);
            $centralAssociation->addCommune($this);
        }

        return $this;
    }

    /**
     * @param CentralAssociation $centralAssociation
     * @return self
     */
    public function removeCentralAssociation($centralAssociation): self
    {
        if ($this->centralAssociations->contains($centralAssociation)) {
            $this->centralAssociations->removeElement($centralAssociation);
            $centralAssociation->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return CentralAssociation[]|Collection
     */
    public function getCentralAssociations()
    {
        return $this->centralAssociations;
    }

    /**
     * @param CentralAssociation[]|Collection $centralAssociations
     */
    public function setCentralAssociations($centralAssociations): void
    {
        $this->centralAssociations = $centralAssociations;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function addLaboratory($laboratory): self
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            //$laboratory->addCommune($this);
        }

        return $this;
    }

    /**
     * @param Laboratory $laboratory
     * @return self
     */
    public function removeLaboratory($laboratory): self
    {
        if ($this->laboratories->contains($laboratory)) {
            $this->laboratories->removeElement($laboratory);
            //$laboratory->removeCommune($this);
        }

        return $this;
    }

    /**
     * @return Laboratory[]|Collection
     */
    public function getLaboratories()
    {
        return $this->laboratories;
    }

    /**
     * @param Laboratory[]|Collection $laboratories
     */
    public function setLaboratories($laboratories): void
    {
        $this->laboratories = $laboratories;
    }

    /**
     * @return Commune|null
     */
    public function getConstituency(): ?Commune
    {
        return $this->constituency;
    }

    /**
     * @param Commune|null $constituency
     */
    public function setConstituency(?Commune $constituency): void
    {
        $this->constituency = $constituency;
    }

    /**
     * @return AdministrativeDistrict|null
     */
    public function getAdministrativeDistrict(): ?AdministrativeDistrict
    {
        return $this->administrativeDistrict;
    }

    /**
     * @param AdministrativeDistrict|null $administrativeDistrict
     */
    public function setAdministrativeDistrict(?AdministrativeDistrict $administrativeDistrict): void
    {
        $this->administrativeDistrict = $administrativeDistrict;
    }

    /**
     * @return CommuneType|null
     */
    public function getCommuneType(): ?CommuneType
    {
        return $this->communeType;
    }

    /**
     * @param CommuneType|null $communeType
     */
    public function setCommuneType(?CommuneType $communeType): void
    {
        $this->communeType = $communeType;
    }

    /**
     * @return string|null
     */
    public function getMainEmail(): ?string
    {
        return $this->mainEmail;
    }

    /**
     * @param string|null $mainEmail
     */
    public function setMainEmail(?string $mainEmail): void
    {
        $this->mainEmail = $mainEmail;
    }

    /**
     * @return string|null
     */
    public function getOfficialCommunityKey(): ?string
    {
        return $this->officialCommunityKey;
    }

    /**
     * @param string|null $officialCommunityKey
     */
    public function setOfficialCommunityKey(?string $officialCommunityKey): void
    {
        $this->officialCommunityKey = $officialCommunityKey;
    }

    /**
     * Returns the unique manufacturer list with the manufacturer determined through the specialized procedures
     * @return Collection
     */
    public function getManufacturers(): Collection
    {
        $manufacturers = new ArrayCollection();
        $specializedProcedures = $this->getSpecializedProcedures();
        foreach ($specializedProcedures as $specializedProcedure) {
            $spManufacturers = $specializedProcedure->getManufacturers();
            foreach ($spManufacturers as $manufacturer) {
                if (!$manufacturers->contains($manufacturer)) {
                    $manufacturers->add($manufacturer);
                }
            }
        }
        return $manufacturers;
    }
}
