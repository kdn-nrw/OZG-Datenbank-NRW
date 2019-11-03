<?php
/**
 * Base entity trait
 *
 **
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Entity\Base;

use DateTime;

/**
 * Interface SoftdeletableEntityInterface
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
interface SoftdeletableEntityInterface
{

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime;

    /**
     * @param DateTime|null $deletedAt
     * @return self
     */
    public function setDeletedAt(?DateTime $deletedAt);

}
