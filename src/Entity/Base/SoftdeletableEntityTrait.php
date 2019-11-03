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
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait SoftdeletableEntityTrait
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
trait SoftdeletableEntityTrait
{

    /**
     * @var null|DateTime
     *
     * DateTime the entity was marked as deleted
     * @ORM\Column(nullable=true, type="datetime", name="deleted_at")
     */
    protected $deletedAt = null;

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime|null $deletedAt
     * @return self
     */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

}
