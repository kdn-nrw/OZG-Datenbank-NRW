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

namespace App\Entity\Base;


/**
 * interface Person
 */
interface PersonInterface extends BaseEntityInterface
{
    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;
    public const GENDER_OTHER = 2;
    public const GENDER_UNKNOWN = 3;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return int
     */
    public function getGender(): int;
}
