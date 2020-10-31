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

use Doctrine\Common\Collections\Collection;

/**
 * HasUploadsEntityInterface interface
 */
interface HasDocumentsEntityInterface extends HasUploadsEntityInterface
{
    /**
     * Get the entity documents
     *
     * @return Collection
     */
    public function getDocuments(): Collection;
}
