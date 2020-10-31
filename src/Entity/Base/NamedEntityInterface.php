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

namespace App\Entity\Base;


/**
 * Interface NamedEntityInterface
 */
interface NamedEntityInterface extends BaseEntityInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name);

    /**
     * @return string
     */
    public function __toString(): string;
}
