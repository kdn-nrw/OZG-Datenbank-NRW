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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait TimestampableEntityTrait
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
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
