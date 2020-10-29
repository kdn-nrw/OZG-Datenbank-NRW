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

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait SoftdeletableEntityTrait
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
