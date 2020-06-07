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

use App\Entity\Base\AppBaseEntity;
use App\Entity\Base\SoftdeletableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Commune
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\CommuneRepository")
 * @ORM\Table(name="ozg_commune")
 */
class Commune extends AppBaseEntity implements OrganisationEntityInterface
{
    use AddressTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="Organisation", inversedBy="commune", cascade={"all"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $organisation;

    /**
     * Contact persons
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @var Office[]|Collection
     * @ORM\OneToMany(targetEntity="Office", mappedBy="commune", cascade={"all"}, orphanRemoval=true)
     * @deprecated
     */
    private $offices;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceProvider", inversedBy="communes")
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
     * @ORM\ManyToMany(targetEntity="Solution", mappedBy="communes")
     */
    private $solutions;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="SpecializedProcedure", mappedBy="communes")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $specializedProcedures;

    /**
     * @var Portal[]|Collection
     * @ORM\ManyToMany(targetEntity="Portal", inversedBy="communes")
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
     * @ORM\ManyToMany(targetEntity="CentralAssociation", inversedBy="communes")
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

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
        $this->centralAssociations = new ArrayCollection();
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
     * @return string|null
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @param string|null $contact
     */
    public function setContact(?string $contact): void
    {
        $this->contact = $contact;
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
     * Returns the unique manufacturer list with the manufacturer determined through the specialized procedures
     * @return ArrayCollection
     */
    public function getManufacturers(): ArrayCollection
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
