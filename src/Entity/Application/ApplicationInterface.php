<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Application;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\SpecializedProcedure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ApplicationInterface
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_specialized_procedure_interface")
 */
class ApplicationInterface extends BaseNamedEntity
{
    /**
     * @var SpecializedProcedure
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecializedProcedure", inversedBy="applicationInterfaces", cascade={"persist"})
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $application;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @var SpecializedProcedure[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\SpecializedProcedure")
     * @ORM\JoinTable(name="ozg_specialized_procedure_interface_connected",
     *     joinColumns={
     *     @ORM\JoinColumn(name="application_interface_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $connectedSpecializedProcedures;

    public function __construct()
    {
        $this->connectedSpecializedProcedures = new ArrayCollection();
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getApplication(): ?SpecializedProcedure
    {
        return $this->application;
    }

    /**
     * @param SpecializedProcedure $application
     */
    public function setApplication(SpecializedProcedure $application): void
    {
        $this->application = $application;
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
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function addSpecializedProcedure(SpecializedProcedure $specializedProcedure): self
    {
        if (!$this->connectedSpecializedProcedures->contains($specializedProcedure)) {
            $this->connectedSpecializedProcedures->add($specializedProcedure);
        }

        return $this;
    }

    /**
     * @param SpecializedProcedure $specializedProcedure
     * @return self
     */
    public function removeSpecializedProcedure(SpecializedProcedure $specializedProcedure): self
    {
        if ($this->connectedSpecializedProcedures->contains($specializedProcedure)) {
            $this->connectedSpecializedProcedures->removeElement($specializedProcedure);
        }

        return $this;
    }

    /**
     * @return SpecializedProcedure[]|Collection
     */
    public function getConnectedSpecializedProcedures()
    {
        return $this->connectedSpecializedProcedures;
    }

    /**
     * @param SpecializedProcedure[]|Collection $connectedSpecializedProcedures
     */
    public function setConnectedSpecializedProcedures($connectedSpecializedProcedures): void
    {
        $this->connectedSpecializedProcedures = $connectedSpecializedProcedures;
    }

    public function __toString(): string
    {
        return $this->getName() ?: 'n/a';
    }

}
