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
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Class form solution
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_form_solution")
 * @Vich\Uploadable
 */
class FormSolution extends AbstractOnboardingEntity
{

    /**
     * Contacts for this entity
     *
     * @var ArrayCollection|Contact[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\Contact", mappedBy="formSolution", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * Privacy url
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
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
     * Letterhead_address
     *
     * @var string|null
     *
     * @ORM\Column(name="letterhead_address", type="text", nullable=true)
     */
    protected $letterheadAddress;

    public function __construct(Commune $commune)
    {
        parent::__construct($commune);
        $this->contacts = new ArrayCollection();
    }

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        $collection = $this->contacts;
        if ($collection instanceof Collection) {
            $typeChoices = [Contact::CONTACT_TYPE_FS];//Contact::$contactTypeChoices;
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
            $this->modifiedAt = new \DateTimeImmutable();
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
     */
    public function getPrivacyUrl(): ?string
    {
        return $this->privacyUrl;
    }

    /**
     * @param string|null $privacyUrl
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
     */
    public function getImprintUrl(): ?string
    {
        return $this->imprintUrl;
    }

    /**
     * @param string|null $imprintUrl
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
    public function getLetterheadAddress(): ?string
    {
        if (null === $this->letterheadAddress && null !== $this->commune) {
            $communeTypeName = $this->commune->getCommuneType() . '';
            if (strpos($communeTypeName, 'Stadt') !== false) {
                $communePrefix = 'Stadt';
            } elseif (strpos($communeTypeName, 'Stadt') !== false) {
                $communePrefix = 'Stadt';
            } elseif ($communeTypeName === 'Kreis') {
                $communePrefix = 'Kreis';
            } else {
                $communePrefix = '';
            }
            $lines = [
                trim($communePrefix . ' '.$this->getCommuneName()),
                'Die Bürgermeisterin / Der Bürgermeister oder ähnliches',
                $this->commune->getStreet(),
                trim($this->commune->getZipCode() . ' ' . $this->commune->getTown()),
            ];
            $this->letterheadAddress = implode("\n", array_filter($lines));
        }
        return $this->letterheadAddress;
    }

    /**
     * @return string|null
     */
    public function getAdministrationEmail(): ?string
    {
        return $this->commune->getAdministrationEmail();
    }

    /**
     * @param string|null $administrationEmail
     */
    public function setAdministrationEmail(?string $administrationEmail): void
    {
        $this->commune->setAdministrationEmail($administrationEmail);
    }

    /**
     * @return string|null
     */
    public function getAdministrationPhoneNumber(): ?string
    {
        return $this->commune->getAdministrationPhoneNumber();
    }

    /**
     * @param string|null $administrationPhoneNumber
     */
    public function setAdministrationPhoneNumber(?string $administrationPhoneNumber): void
    {
        $this->commune->setAdministrationPhoneNumber($administrationPhoneNumber);
    }

    /**
     * @return string|null
     */
    public function getAdministrationFaxNumber(): ?string
    {
        return $this->commune->getAdministrationFaxNumber();
    }

    /**
     * @param string|null $administrationFaxNumber
     */
    public function setAdministrationFaxNumber(?string $administrationFaxNumber): void
    {
        $this->commune->setAdministrationFaxNumber($administrationFaxNumber);
    }

    /**
     * @return string|null
     */
    public function getAdministrationUrl(): ?string
    {
        return $this->commune->getAdministrationUrl();
    }

    /**
     * @param string|null $administrationUrl
     */
    public function setAdministrationUrl(?string $administrationUrl): void
    {
        $this->commune->setAdministrationUrl($administrationUrl);
    }

    /**
     * @param string|null $letterheadAddress
     */
    public function setLetterheadAddress(?string $letterheadAddress): void
    {
        $this->letterheadAddress = $letterheadAddress;
    }
}
