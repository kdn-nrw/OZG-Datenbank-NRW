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

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Contact;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class commune solution
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_solutions_communes")
 */
class CommuneSolution extends BaseNamedEntity
{
    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $communeType = Solution::COMMUNE_TYPE_SELECTED;

    /**
     * @var Commune|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", inversedBy="communeSolutions", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id")
     */
    private $commune;

    /**
     * @var Solution|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Solution", inversedBy="communeSolutions", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     */
    private $solution;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Solution is ready?
     *
     * @var bool|null
     *
     * @ORM\Column(name="connection_planned", type="boolean", nullable=true)
     */
    protected $connectionPlanned;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="connection_planned_at")
     */
    protected $connectionPlannedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var SpecializedProcedure|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecializedProcedure", cascade={"persist"})
     * @ORM\JoinColumn(name="specialized_procedure_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     */
    private $specializedProcedure;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact")
     * @ORM\JoinTable(name="ozg_solutions_communes_contact",
     *     joinColumns={
     *     @ORM\JoinColumn(name="solutions_communes_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    protected $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        if (empty($this->name)  && null !== $this->solution) {
            return $this->solution->getName();
        }
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCommuneType(): string
    {
        return empty($this->communeType) ? Solution::COMMUNE_TYPE_SELECTED : $this->communeType;
    }

    /**
     * @param string|null $communeType
     */
    public function setCommuneType(?string $communeType): void
    {
        $this->communeType = $communeType;
    }

    /**
     * Returns true, if commune is selected (solution not set for all communes)
     *
     * @return bool
     */
    public function isSelectedType(): bool
    {
        return $this->communeType === Solution::COMMUNE_TYPE_SELECTED;
    }

    /**
     * @return Commune|null
     */
    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    /**
     * @param Commune $commune
     */
    public function setCommune(Commune $commune): void
    {
        $this->commune = $commune;
    }

    /**
     * @return Solution|null
     */
    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution(Solution $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        if (empty($this->description) && null !== $this->solution) {
            return $this->solution->getDescription();
        }
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
     * @return bool|null
     */
    public function getConnectionPlanned(): ?bool
    {
        return $this->connectionPlanned;
    }

    /**
     * @param bool|null $connectionPlanned
     */
    public function setConnectionPlanned(?bool $connectionPlanned): void
    {
        $this->connectionPlanned = $connectionPlanned;
    }

    /**
     * @return DateTime|null
     */
    public function getConnectionPlannedAt(): ?DateTime
    {
        return $this->connectionPlannedAt;
    }

    /**
     * @param DateTime|null $connectionPlannedAt
     */
    public function setConnectionPlannedAt(?DateTime $connectionPlannedAt): void
    {
        $this->connectionPlannedAt = $connectionPlannedAt;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getSpecializedProcedure(): ?SpecializedProcedure
    {
        return $this->specializedProcedure;
    }

    /**
     * @param SpecializedProcedure|null $specializedProcedure
     */
    public function setSpecializedProcedure(?SpecializedProcedure $specializedProcedure): void
    {
        $this->specializedProcedure = $specializedProcedure;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->getSolution() . '';
        $commune = $this->getCommune();
        if (null !== $commune) {
            if (!empty($name)) {
                $name .= ', ';
            }
            $name .= $commune . '';
        }
        if (empty($name)) {
            $name = (string)$this->getId();
            if (empty($name)) {
                $name = 'n.a.';
            }
        }
        if (null !== $specializedProcedure = $this->getSpecializedProcedure()) {
            $name .= ' (' . $specializedProcedure->getName() . ')';
        }
        return $name;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param Contact[]|Collection $contacts
     */
    public function setContacts($contacts): void
    {
        $this->contacts = $contacts;
    }

}
