<?php
/**
 * An interface for all entities
 **
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Entity\Base;

/**
 * An interface for all mindbase entities
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
interface BaseEntityInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return string
     */
    public function __toString(): string;
}
