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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait TimestampableEntityTrait
 */
trait TimestampableEntityTrait
{

    /**
     * @var null|DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(nullable=true, type="datetime", name="modified_at")
     */
    protected $modifiedAt;

    /**
     * @var null|DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(nullable=true, type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @return DateTime|null
     */
    public function getModifiedAt(): ?DateTime
    {
        return $this->modifiedAt;
    }

    /**
     * @param DateTime|null $modifiedAt
     * @return self
     */
    public function setModifiedAt(?DateTime $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     * @return self
     */
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
