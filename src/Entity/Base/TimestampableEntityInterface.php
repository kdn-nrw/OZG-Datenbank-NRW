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
 * Interface TimestampableEntityInterface
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
interface TimestampableEntityInterface
{

    /**
     * @return DateTime|null
     */
    public function getModifiedAt(): ?DateTime;

    /**
     * @param DateTime|null $modifiedAt
     * @return self
     */
    public function setModifiedAt(?DateTime $modifiedAt);

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime;

    /**
     * @param DateTime|null $createdAt
     * @return self
     */
    public function setCreatedAt(?DateTime $createdAt);

}
