<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\HideableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Contact
 *
 * @ORM\Entity()
 * @ORM\Table(name="ozg_contact")
 * @ORM\HasLifecycleCallbacks
 */
class Contact extends BaseEntity implements ImportEntityInterface
{
    public const CONTACT_TYPE_DEFAULT = 'default';
    public const CONTACT_TYPE_IMPORT_CMS = 'cms_address';

    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;
    public const GENDER_OTHER = 2;
    public const GENDER_UNKNOWN = 3;

    use CategoryTrait;
    use HideableEntityTrait;
    use AddressTrait;
    use ImportTrait;

    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contactType = self::CONTACT_TYPE_DEFAULT;

    /**
     * @ORM\Column(type="integer", name="gender", nullable=true)
     * @var int
     */
    private $gender = self::GENDER_UNKNOWN;

    /**
     * @ORM\Column(type="string", name="title", length=100, nullable=true)
     * @var string|null
     */
    private $title;

    /**
     * @ORM\Column(type="string", name="first_name", length=100, nullable=true)
     * @var string|null
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", length=100, nullable=true)
     * @var string|null
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", name="email", length=255, nullable=true)
     * @var string|null
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="organisation", length=255, nullable=true)
     * @var string|null
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", name="department", length=255, nullable=true)
     * @var string|null
     */
    private $department;

    /**
     * @ORM\Column(type="string", name="position", length=255, nullable=true)
     * @var string|null
     */
    private $position;

    /**
     * @ORM\Column(type="string", name="phone_number", length=100, nullable=true)
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", name="fax_number", length=100, nullable=true)
     * @var string|null
     */
    private $faxNumber;

    /**
     * @ORM\Column(type="string", name="mobile_number", length=100, nullable=true)
     * @var string|null
     */
    private $mobileNumber;

    /**
     * @var Organisation|null
     * @ORM\ManyToOne(targetEntity="Organisation", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true)
     */
    private $organisationEntity;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", mappedBy="solutionContacts")
     */
    private $solutions;

    /**
     * @var ImplementationProject[]|Collection
     * @ORM\ManyToMany(targetEntity="ImplementationProject", mappedBy="contacts")
     */
    private $implementationProjects;

    /**
     * @var Category[]|Collection
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="contacts")
     * @ORM\JoinTable(name="ozg_contact_category",
     *     joinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *   }
     * )
     */
    private $categories;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->implementationProjects = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getContactType(): string
    {
        if (!$this->contactType) {
            $this->contactType = self::CONTACT_TYPE_DEFAULT;
        }
        return $this->contactType;
    }

    /**
     * @param string|null $contactType
     */
    public function setContactType(?string $contactType): void
    {
        if (!$this->contactType) {
            $contactType = self::CONTACT_TYPE_DEFAULT;
        }
        $this->contactType = $contactType;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        if (null === $this->gender || $this->gender < 0) {
            $this->gender = self::GENDER_UNKNOWN;
        }
        return $this->gender;
    }

    /**
     * @param int|null $gender
     */
    public function setGender(?int $gender): void
    {
        if (null === $gender || $gender < 0) {
            $this->gender = self::GENDER_UNKNOWN;
        } else {
            $this->gender = $gender;
        }
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }


    /**
     * @return string|null
     */
    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    /**
     * @param string|null $organisation
     */
    public function setOrganisation(?string $organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @param string|null $department
     */
    public function setDepartment(?string $department): void
    {
        $this->department = $department;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getFaxNumber(): ?string
    {
        return $this->faxNumber;
    }

    /**
     * @param string|null $faxNumber
     */
    public function setFaxNumber(?string $faxNumber): void
    {
        $this->faxNumber = $faxNumber;
    }

    /**
     * @return string|null
     */
    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    /**
     * @param string|null $mobileNumber
     */
    public function setMobileNumber(?string $mobileNumber): void
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return Organisation|null
     */
    public function getOrganisationEntity(): ?Organisation
    {
        return $this->organisationEntity;
    }

    /**
     * @param Organisation|null $organisationEntity
     */
    public function setOrganisationEntity(?Organisation $organisationEntity): void
    {
        $this->organisationEntity = $organisationEntity;
        if (null !== $organisationEntity && empty($this->organisation)) {
            $this->organisation = $organisationEntity->getName();
        }
    }

    public function getDisplayName(): string
    {
        $name = $this->getFullName();
        $organisation = $this->getOrganisation();
        if ($organisation) {
            $name .= ' (' . $organisation . ')';
        }
        return $name;
    }

    public function getFullName(): string
    {
        $name = trim($this->getFirstName() . ' ' . $this->getLastName());
        if ($name) {
            $title = $this->getTitle();
            if ($title) {
                $name = $title . ' ' . $name;
            }
        }
        return $name;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addSolutionContact($this);
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
            $solution->removeSolutionContact($this);
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
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function addImplementationProject($implementationProject): self
    {
        if (!$this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->add($implementationProject);
            $implementationProject->addContact($this);
        }

        return $this;
    }

    /**
     * @param ImplementationProject $implementationProject
     * @return self
     */
    public function removeImplementationProject($implementationProject): self
    {
        if ($this->implementationProjects->contains($implementationProject)) {
            $this->implementationProjects->removeElement($implementationProject);
            $implementationProject->removeContact($this);
        }

        return $this;
    }

    /**
     * @return ImplementationProject[]|Collection
     */
    public function getImplementationProjects()
    {
        return $this->implementationProjects;
    }

    /**
     * @param ImplementationProject[]|Collection $implementationProjects
     */
    public function setImplementationProjects($implementationProjects): void
    {
        $this->implementationProjects = $implementationProjects;
    }

    /**
     * Returns the name of this contact
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDisplayName();
    }


}
