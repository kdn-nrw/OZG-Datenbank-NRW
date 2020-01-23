<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class implementation project
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation_project")
 * @ORM\HasLifecycleCallbacks
 */
class ImplementationProject extends BaseNamedEntity
{

    /**
     * Status
     * @var ImplementationStatus|null
     *
     * @ORM\ManyToOne(targetEntity="ImplementationStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Notes
     *
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes = '';

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceSystem", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_service_system",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_system_id", referencedColumnName="id")
     *   }
     * )
     */
    private $serviceSystems;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_solution",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutions;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="implementationProjects")
     * @ORM\JoinTable(name="ozg_implementation_project_contact",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $contacts;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="Contact")
     * @ORM\JoinTable(name="ozg_implementation_project_contact_interested",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $interestedContacts;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="Contact")
     * @ORM\JoinTable(name="ozg_implementation_project_contact_participation",
     *     joinColumns={
     *     @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   }
     * )
     */
    private $participationContacts;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->serviceSystems = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->interestedContacts = new ArrayCollection();
        $this->participationContacts = new ArrayCollection();
    }

    /**
     * @return ImplementationStatus|null
     */
    public function getStatus(): ?ImplementationStatus
    {
        return $this->status;
    }

    /**
     * @param ImplementationStatus|null $status
     */
    public function setStatus(?ImplementationStatus $status): void
    {
        $this->status = $status;
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
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @param ServiceSystem $serviceSystem
     * @return self
     */
    public function addServiceSystem($serviceSystem): self
    {
        if (!$this->serviceSystems->contains($serviceSystem)) {
            $this->serviceSystems->add($serviceSystem);
            $serviceSystem->addImplementationProject($this);
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
            $serviceSystem->removeImplementationProject($this);
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
     * Returns the subjects of the referenced service systems
     *
     * @return Subject[]
     */
    public function getSubjects(): array
    {
        $subjects = [];
        $serviceSystems = $this->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            if (null !== $serviceSystem->getSituation()
                && null !== $subject = $serviceSystem->getSituation()->getSubject()) {
                if (!isset($subjects[$subject->getId()])) {
                    $subjects[$subject->getId()] = $subject;
                }
            }
        }
        return $subjects;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addImplementationProject($this);
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
            $solution->removeImplementationProject($this);
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
     * @return Laboratory[]
     */
    public function getLaboratories(): array
    {
        $items = [];
        $serviceSystems = $this->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            $laboratories = $serviceSystem->getLaboratories();
            foreach ($laboratories as $laboratory) {
                if (!isset($items[$laboratory->getId()])) {
                    $items[$laboratory->getId()] = $laboratory;
                }
            }
        }
        return $items;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact($contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->removeImplementationProject($this);
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

    /**
     * @param Contact $interestedContact
     * @return self
     */
    public function addInterestedContact($interestedContact): self
    {
        if (!$this->interestedContacts->contains($interestedContact)) {
            $this->interestedContacts->add($interestedContact);
            //$interestedContact->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Contact $interestedContact
     * @return self
     */
    public function removeInterestedContact($interestedContact): self
    {
        if ($this->interestedContacts->contains($interestedContact)) {
            $this->interestedContacts->removeElement($interestedContact);
            //$interestedContact->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getInterestedContacts()
    {
        return $this->interestedContacts;
    }

    /**
     * @param Contact[]|Collection $interestedContacts
     */
    public function setInterestedContacts($interestedContacts): void
    {
        $this->interestedContacts = $interestedContacts;
    }

    /**
     * @param Contact $participationContact
     * @return self
     */
    public function addParticipationContact($participationContact): self
    {
        if (!$this->participationContacts->contains($participationContact)) {
            $this->participationContacts->add($participationContact);
            //$participationContact->addImplementationProject($this);
        }

        return $this;
    }

    /**
     * @param Contact $participationContact
     * @return self
     */
    public function removeParticipationContact($participationContact): self
    {
        if ($this->participationContacts->contains($participationContact)) {
            $this->participationContacts->removeElement($participationContact);
            //$participationContact->removeImplementationProject($this);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getParticipationContacts()
    {
        return $this->participationContacts;
    }

    /**
     * @param Contact[]|Collection $participationContacts
     */
    public function setParticipationContacts($participationContacts): void
    {
        $this->participationContacts = $participationContacts;
    }
}
