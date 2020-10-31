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

/**
 * HasImageEntityInterface interface
 */
interface HasImageEntityInterface extends HasUploadsEntityInterface
{
    public const FILTER_NAME_DEFAULT = 'default_content_image';

    /**
     * Get the entity image
     *
     * @return DocumentUploadEntityInterface|null
     */
    public function getImage(): ?DocumentUploadEntityInterface;
}
