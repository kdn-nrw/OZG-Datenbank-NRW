<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\DocumentUploadEntityInterface;
use App\Entity\Base\HasUploadsEntityInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * ModelRegionProjectDocument
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_project_document")
 * @Vich\Uploadable
 */
class ModelRegionProjectDocument extends BaseNamedEntity implements DocumentUploadEntityInterface
{
    /**
     * @Vich\UploadableField(mapping="model_region_project_document", fileNameProperty="localName", originalName="name", size="fileSize")
     * @Assert\File(maxSize = "10M")
     *
     * @var File
     */
    private $file;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $fileSize;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $localName;

    /**
     * @var ModelRegionProject|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelRegionProject", inversedBy="documents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $modelRegionProject;

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
     * Set model region project
     *
     * @param ModelRegionProject|null $modelRegionProject
     *
     * @return self
     */
    public function setModelRegionProject(?ModelRegionProject $modelRegionProject): self
    {
        $this->modelRegionProject = $modelRegionProject;

        return $this;
    }

    /**
     * Get model region project
     *
     * @return ModelRegionProject|null
     */
    public function getModelRegionProject(): ModelRegionProject
    {
        return $this->modelRegionProject;
    }

    /**
     * Get parent entity referene
     *
     * @return HasUploadsEntityInterface
     */
    public function getParent(): HasUploadsEntityInterface
    {
        return $this->getModelRegionProject();
    }
}