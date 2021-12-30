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

use App\Entity\Base\BaseEntity;
use App\Entity\Base\ContactPropertiesTrait;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\PersonInterface;
use App\Entity\Base\PersonPropertiesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Class Contact
 *
 * @ORM\Entity()
 * @ORM\Table(name="ozg_contact")
 * @Vich\Uploadable
 */
class Contact extends BaseEntity implements ImportEntityInterface, HasSolutionsEntityInterface, HideableEntityInterface, PersonInterface
{
    public const CONTACT_TYPE_DEFAULT = 'default';
    public const CONTACT_TYPE_IMPORT_CMS = 'cms_address';

    use PersonPropertiesTrait;
    use ContactPropertiesTrait;

    use CategoryTrait;
    use HideableEntityTrait;
    use AddressTrait;
    use ImportTrait;
    use UrlTrait;

    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contactType = self::CONTACT_TYPE_DEFAULT;

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
     * @ORM\Column(type="string", name="fax_number", length=100, nullable=true)
     * @var string|null
     */
    private $faxNumber;

    /**
     * @var Organisation|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $organisationEntity;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Solution", mappedBy="solutionContacts")
     */
    private $solutions;

    /**
     * @var ImplementationProject[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ImplementationProject", mappedBy="contacts")
     */
    private $implementationProjects;

    /**
     * @var Category[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="contacts")
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

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="contact_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $imageSize;


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
     * @return string|null
     */
    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    /**
     * @param string|null $organisation
     */
    public function setOrganisation(?string $organisation): void
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
    public function getSolutions(): Collection
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->modifiedAt = new \DateTime();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
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
