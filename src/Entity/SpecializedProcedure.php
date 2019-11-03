<?php

namespace App\Entity;

use App\Entity\Base\BaseBlamableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;


/**
 * Class SpecializedProcedure (Fachverfahren)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_specialized_procedure")
 * @ORM\HasLifecycleCallbacks
 */
class SpecializedProcedure extends BaseBlamableEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", mappedBy="specializedProcedures")
     */
    private $solutions;

    /**
     * @var Manufacturer[]|Collection
     * @ORM\ManyToMany(targetEntity="Manufacturer", mappedBy="specializedProcedures")
     */
    private $manufacturers;


    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->manufacturers = new ArrayCollection();
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
     * @param Manufacturer $manufacturer
     * @return self
     */
    public function addManufacturer($manufacturer)
    {
        if (!$this->manufacturers->contains($manufacturer)) {
            $this->manufacturers->add($manufacturer);
            $manufacturer->addSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @param Manufacturer $manufacturer
     * @return self
     */
    public function removeManufacturer($manufacturer)
    {
        if ($this->manufacturers->contains($manufacturer)) {
            $this->manufacturers->removeElement($manufacturer);
            $manufacturer->removeSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @return Manufacturer[]|Collection
     */
    public function getManufacturers()
    {
        return $this->manufacturers;
    }

    /**
     * @param Manufacturer[]|Collection $manufacturers
     */
    public function setManufacturers($manufacturers): void
    {
        $this->manufacturers = $manufacturers;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution)
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addSpecializedProcedure($this);
        }

        return $this;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function removeSolution($solution)
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeSpecializedProcedure($this);
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
}
