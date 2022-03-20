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

namespace App\Model;

use App\Entity\Base\DocumentUploadEntityInterface;
use App\Entity\Base\HasUploadsEntityInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document model
 */
class Document implements DocumentUploadEntityInterface
{

    /**
     * @var null|int
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var File|UploadedFile|null
     */
    private $file;

    /**
     * @var int|null
     */
    private $fileSize;

    /**
     * @var string|null
     */
    protected $localName;

    /**
     * @var HasUploadsEntityInterface|null
     */
    private $parent;

    public function __construct(HasUploadsEntityInterface $parent, int $id)
    {
        $this->parent = $parent;
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get original name
     *
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->getName();
    }

    /**
     * @param File|UploadedFile $file
     *
     * @return self
     */
    public function setFile(File $file = null): self
    {
        $this->file = $file;
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
     * @return HasUploadsEntityInterface
     */
    public function getParent(): HasUploadsEntityInterface
    {
        return $this->parent;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->name) {
            return 'NULL';
        }
        return (string)$this->getName();
    }
}