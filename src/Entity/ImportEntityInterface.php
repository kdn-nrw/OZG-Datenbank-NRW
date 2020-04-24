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


/**
 * Import entity interface
 */
interface ImportEntityInterface
{
    /**
     * @return int|null
     */
    public function getImportId(): ?int;

    /**
     * @param int|null $importId
     */
    public function setImportId(?int $importId): void;

    /**
     * @return string|null
     */
    public function getImportSource(): ?string;

    /**
     * @param string|null $importSource
     */
    public function setImportSource(?string $importSource): void;

}
