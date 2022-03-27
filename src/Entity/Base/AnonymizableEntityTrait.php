<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Base;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait AnonymizableEntityTrait
 * @see AnonymizableEntityInterface (provides field "anonymizedAt")
 */
trait AnonymizableEntityTrait
{
    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="anonymized_at", type="datetime", nullable=true)
     */
    protected $anonymizedAt;

    /**
     * @return DateTime|null
     */
    public function getAnonymizedAt(): ?DateTime
    {
        return $this->anonymizedAt;
    }

    /**
     * @param DateTime|null $anonymizedAt
     * @return AnonymizableEntityTrait
     */
    public function setAnonymizedAt(?DateTime $anonymizedAt): self
    {
        $this->anonymizedAt = $anonymizedAt;

        return $this;
    }
}
