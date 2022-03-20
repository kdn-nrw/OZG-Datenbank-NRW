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

namespace App\Entity\Base;

use Symfony\Component\HttpFoundation\File\File;

/**
 * DocumentUploadEntityInterface interface
 */
interface DocumentUploadEntityInterface extends NamedEntityInterface
{
    /**
     * @return File|null
     */
    public function getFile(): ?File;

    /**
     * @return int|null
     */
    public function getFileSize(): ?int;

    /**
     * @return string|null
     */
    public function getLocalName(): ?string;

    /**
     * Get parent entity reference
     *
     * @return HasUploadsEntityInterface
     */
    public function getParent(): HasUploadsEntityInterface;
}
