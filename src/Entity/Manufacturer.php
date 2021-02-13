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
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class manufacturer
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_manufacturer")
 * @ApiResource
 */
class Manufacturer extends BaseNamedEntity implements OrganisationEntityInterface
{
    use AddressTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="App\Entity\Organisation", inversedBy="manufacturer", cascade={"all"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $organisation;

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure", inversedBy="manufacturers")
     * @ORM\JoinTable(name="ozg_manufacturers_specialized_procedures",
     *     joinColumns={
     *     @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $specializedProcedures;

    public function __construct()
    {
        $this->specializedProcedures = new ArrayCollection();
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setManufacturer($this);
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure($specializedProcedure): self
    {
        if (!$this->specializedProcedures->contains($specializedProcedure)) {
            $this->specializedProcedures->add($specializedProcedure);
            $specializedProcedure->addManufacturer($this);
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
            $specializedProcedure->removeManufacturer($this);
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
}
