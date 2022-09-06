<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Onboarding;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\DocumentUploadEntityInterface;
use App\Entity\Base\HasUploadsEntityInterface;
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Onboarding document
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_document")
 * @Vich\Uploadable
 */
class OnboardingDocument extends BaseNamedEntity implements DocumentUploadEntityInterface, CalculateCompletenessEntityInterface
{
    public const DOCUMENT_TYPE_GENERAL = 'general';

    /**
     * @var array Supported payment provider types
     */
    public static $documentTypeChoices = [
        'app.abstract_onboarding_entity.entity.document_type_choices.general' => self::DOCUMENT_TYPE_GENERAL,
        'app.xta_server.entity.document_type_choices.osci_public_key_file' => XtaServer::DOCUMENT_TYPE_PUBLIC_KEY,
        'app.xta_server.entity.document_type_choices.osci_private_key_file' => XtaServer::DOCUMENT_TYPE_PRIVATE_KEY,
        'app.monument_authority.entity.document_type_choices.osci_public_key_file' => MonumentAuthority::DOCUMENT_TYPE_PUBLIC_KEY,
        'app.monument_authority.entity.document_type_choices.osci_private_key_file' => MonumentAuthority::DOCUMENT_TYPE_PRIVATE_KEY,
    ];

    /**
     * Document type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $documentType = self::DOCUMENT_TYPE_GENERAL;

    /**
     * @Vich\UploadableField(mapping="onboarding_document", fileNameProperty="localName", originalName="name", size="fileSize")
     * @Assert\File(maxSize = "10M")
     *
     * @var File
     */
    protected $file;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $fileSize;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $localName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\AbstractOnboardingEntity", inversedBy="documents", cascade={"persist"})
     * @ORM\JoinColumn(name="onboarding_id", referencedColumnName="id", nullable=true)
     * @var AbstractOnboardingEntity|null
     */
    protected $onboarding;

    /**
     * OnboardingDocument constructor.
     * @param AbstractOnboardingEntity|null $onboarding
     * @param string|null $documentType
     */
    public function __construct(?AbstractOnboardingEntity $onboarding, ?string $documentType = null)
    {
        $this->onboarding = $onboarding;
        if ($documentType) {
            $this->documentType = $documentType;
        }
    }

    /**
     * @return string|null
     */
    public function getDocumentType(): ?string
    {
        if (!$this->documentType || !in_array($this->documentType, self::$documentTypeChoices, true)) {
            $this->documentType = self::DOCUMENT_TYPE_GENERAL;
        }
        return $this->documentType;
    }

    /**
     * @param string|null $documentType
     */
    public function setDocumentType(?string $documentType): void
    {
        $this->documentType = $documentType;
    }

    /**
     * @param File|UploadedFile $file
     *
     * @return self
     */
    public function setFile(File $file = null): self
    {
        $this->file = $file;
        if ($file) {
            $this->modifiedAt = new DateTime();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @return int|null
     */
    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    /**
     * @param int|null $fileSize
     */
    public function setFileSize(?int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string|null
     */
    public function getLocalName(): ?string
    {
        return $this->localName;
    }

    /**
     * @param string|null $localName
     */
    public function setLocalName(?string $localName): void
    {
        $this->localName = $localName;
    }

    /**
     * @return AbstractOnboardingEntity|null
     */
    public function getOnboarding(): ?AbstractOnboardingEntity
    {
        return $this->onboarding;
    }

    /**
     * @param AbstractOnboardingEntity|null $onboarding
     */
    public function setOnboarding(?AbstractOnboardingEntity $onboarding): void
    {
        $this->onboarding = $onboarding;
    }

    /**
     * Get parent entity reference
     *
     * @return HasUploadsEntityInterface
     */
    public function getParent(): HasUploadsEntityInterface
    {
        return $this->getOnboarding();
    }
}