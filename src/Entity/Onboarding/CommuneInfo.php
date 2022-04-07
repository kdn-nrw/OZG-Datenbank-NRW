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

namespace App\Entity\Onboarding;

use App\Entity\StateGroup\Commune;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SimpleThings\EntityAudit\Collection\AuditedCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Class commune info
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_base_info")
 * @Vich\Uploadable
 */
class CommuneInfo extends AbstractOnboardingEntity
{

    /**
     * Contacts for this entity
     *
     * @var ArrayCollection|Contact[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\Contact", mappedBy="communeInfo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $contacts;

    /**
     * Privacy text
     *
     * @var string|null
     *
     * @ORM\Column(name="privacy_text", type="text", nullable=true)
     */
    protected $privacyText;

    /**
     * Privacy url
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     * @deprecated
     */
    protected $privacyUrl;


    /**
     * Imprint text
     *
     * @var string|null
     *
     * @ORM\Column(name="imprint_text", type="text", nullable=true)
     */
    protected $imprintText;

    /**
     * Imprint url
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     * @deprecated
     */
    protected $imprintUrl;

    /**
     * Accessibility text
     *
     * @var string|null
     *
     * @ORM\Column(name="accessibility", type="text", nullable=true)
     */
    protected $accessibility;

    /**
     * Opening hours
     *
     * @var string|null
     *
     * @ORM\Column(name="opening_hours", type="text", nullable=true)
     */
    protected $openingHours;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="commune_info_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File|null
     */
    protected $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $imageSize;

    /**
     * Commune solutions for this entity
     *
     * @var ArrayCollection|OnboardingCommuneSolution[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\OnboardingCommuneSolution", mappedBy="communeInfo", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $communeSolutions;

    /**
     * @ORM\Column(type="string", name="ip_address", length=255, nullable=true)
     * @var string|null
     */
    protected $ipAddress;

    /**
     * The completeness of the data has been confirmed
     *
     * @var bool
     *
     * @ORM\Column(name="allow_admin_access", type="boolean", nullable=true)
     */
    protected $allowAdminAccess = false;

    public function __construct(Commune $commune)
    {
        parent::__construct($commune);
        $this->contacts = new ArrayCollection();
        $this->communeSolutions = new ArrayCollection();
    }

    /**
     * @param Contact $contact
     * @param Contact $contact
     * @return self
     * /
     * public function addContact($contact): self
     * {
     * if (!$this->contacts->contains($contact)) {
     * $this->contacts->add($contact);
     * }
     *
     * return $this;
     * }
     *
     * /**
     * @return self
     * /
     * public function removeContact($contact): self
     * {
     * if ($this->contacts->contains($contact)) {
     * $this->contacts->removeElement($contact);
     * }
     *
     * return $this;
     * }*/

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        $collection = $this->contacts;
        // Prevent exception when loading audits; only used by AuditReader
        if ($collection instanceof AuditedCollection) {
            return $collection;
        }
        if ($collection instanceof Collection) {
            $typeChoices = Contact::$contactTypeChoices;
            $order = [];
            $sorting = 1;
            $missingTypes = [];
            foreach ($typeChoices as $typeKey) {
                $order[$typeKey] = $sorting;
                $missingTypes[$typeKey] = true;
                ++$sorting;
            }
            foreach ($collection as $entity) {
                $missingTypes[$entity->getContactType()] = false;
            }
            foreach (array_keys(array_filter($missingTypes)) as $type) {
                $collection->add(new Contact($this, $type));
            }
            $iterator = $collection->getIterator();
            $iterator->uasort(static function (Contact $a, Contact $b) use ($order) {
                $orderA = $order[$a->getContactType()] ?? 999;
                $orderB = $order[$b->getContactType()] ?? 999;
                return ($orderA < $orderB) ? -1 : 1;
            });
            /** @var Contact[] $sortedEntities */
            $sortedEntities = iterator_to_array($iterator);
            $collection->clear();
            $addedTypes = [];
            foreach ($sortedEntities as $contact) {
                if ($addedTypes[$contact->getContactType()] ?? false) {
                    continue;
                }
                $collection->add($contact);
                $addedTypes[$contact->getContactType()] = true;
            }
        }
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
     * @return OnboardingCommuneSolution[]|Collection
     */
    public function getCommuneSolutions()
    {
        return $this->communeSolutions;
    }

    /**
     * @param OnboardingCommuneSolution[]|Collection $communeSolutions
     */
    public function setCommuneSolutions($communeSolutions): void
    {
        $this->communeSolutions = $communeSolutions;
    }

    /**
     * @param OnboardingCommuneSolution $communeSolution
     * @return self
     */
    public function addCommuneSolution(EpaymentService $communeSolution): self
    {
        if (!$this->communeSolutions->contains($communeSolution)) {
            $this->communeSolutions->add($communeSolution);
            $communeSolution->setCommuneInfo($this);
        }

        return $this;
    }

    /**
     * @param OnboardingCommuneSolution $communeSolution
     * @return self
     */
    public function removeCommuneSolution($communeSolution): self
    {
        if ($this->communeSolutions->contains($communeSolution)) {
            $this->communeSolutions->removeElement($communeSolution);
        }

        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return Contact|null
     */
    public function getContactByType(?string $type = null): ?Contact
    {
        $collection = $this->getContacts();
        foreach ($collection as $contact) {
            if ($contact->getContactType() === $type) {
                return $contact;
            }
        }
        return null;
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
     * @return string|null
     */
    public function getPrivacyText(): ?string
    {
        return $this->privacyText;
    }

    /**
     * @param string|null $privacyText
     */
    public function setPrivacyText(?string $privacyText): void
    {
        $this->privacyText = $privacyText;
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getPrivacyUrl(): ?string
    {
        return $this->privacyUrl;
    }

    /**
     * @param string|null $privacyUrl
     * @deprecated
     */
    public function setPrivacyUrl(?string $privacyUrl): void
    {
        $this->privacyUrl = $privacyUrl;
    }

    /**
     * @return string|null
     */
    public function getImprintText(): ?string
    {
        return $this->imprintText;
    }

    /**
     * @param string|null $imprintText
     */
    public function setImprintText(?string $imprintText): void
    {
        $this->imprintText = $imprintText;
    }

    /**
     * @return string|null
     * @deprecated
     */
    public function getImprintUrl(): ?string
    {
        return $this->imprintUrl;
    }

    /**
     * @param string|null $imprintUrl
     * @deprecated
     */
    public function setImprintUrl(?string $imprintUrl): void
    {
        $this->imprintUrl = $imprintUrl;
    }

    /**
     * @return string|null
     */
    public function getAccessibility(): ?string
    {
        return $this->accessibility;
    }

    /**
     * @param string|null $accessibility
     */
    public function setAccessibility(?string $accessibility): void
    {
        $this->accessibility = $accessibility;
    }

    /**
     * @return string|null
     */
    public function getOpeningHours(): ?string
    {
        return $this->openingHours;
    }

    /**
     * @param string|null $openingHours
     */
    public function setOpeningHours(?string $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param string|null $ipAddress
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return bool
     */
    public function isAllowAdminAccess(): bool
    {
        return (bool) $this->allowAdminAccess;
    }

    /**
     * @param bool $allowAdminAccess
     */
    public function setAllowAdminAccess(bool $allowAdminAccess): void
    {
        $this->allowAdminAccess = $allowAdminAccess;
    }

}
