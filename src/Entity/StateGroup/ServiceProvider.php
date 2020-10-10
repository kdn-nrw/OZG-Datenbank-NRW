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
use App\Entity\Base\BaseNamedEntity;
use App\Entity\ContactTextTrait;
use App\Entity\HasManufacturerEntityInterface;
use App\Entity\Laboratory;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use App\Entity\OrganisationTrait;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use App\Entity\UrlTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ServiceProvider
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_provider")
 */
class ServiceProvider extends BaseNamedEntity implements OrganisationEntityInterface, HasManufacturerEntityInterface
{
    use AddressTrait;
    use ContactTextTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="App\Entity\Organisation", inversedBy="serviceProvider", cascade={"all"})
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
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $solutions;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $communes;

    /**
     * @var Laboratory[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Laboratory", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $laboratories;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure", mappedBy="serviceProviders")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $specializedProcedures;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->laboratories = new ArrayCollection();
        $this->specializedProcedures = new ArrayCollection();
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setServiceProvider($this);
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
     * @param Solution $solution
     */
    public function addSolution($solution): void
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addServiceProvider($this);
        }
    }

    /**
     * @param Solution $solution
     */
    public function removeSolution($solution): void
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeServiceProvider($this);
        }
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
     * @param Commune $commune
     * @return ServiceProvider
     */
    public function addCommune($commune): ServiceProvider
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->addServiceProvider($this);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return ServiceProvider
     */
    public function removeCommune($commune): ServiceProvider
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
            $commune->removeServiceProvider($this);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
    }

    /**
     * @param Laboratory $laboratory
     * @return ServiceProvider
     */
    public function addLaboratory($laboratory): ServiceProvider
    {
        if (!$this->laboratories->contains($laboratory)) {
            $this->laboratories->add($laboratory);
            $laboratory->addServiceProvider($this);
        }

        return $this;
    }

    /**
     * @param Laboratory $laboratory
     * @return ServiceProvider
     */
    public function removeLaboratory($laboratory): ServiceProvider
    {
        if ($this->laboratories->contains($laboratory)) {
            $this->laboratories->removeElement($laboratory);
            $laboratory->removeServiceProvider($this);
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
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addServiceProvider($this);
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
            $specializedProcedure->removeServiceProvider($this);
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
