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

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Vertrauensniveau
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_confidence_level")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class ConfidenceLevel extends BaseNamedEntity
{

    /**
     * vtniveaustufe
     * @var int
     *
     * @ORM\Column(name="availability", type="integer")
     */
    private $availability = 0;

    /**
     * vtniveauerkl
     * @var string|null
     *
     * @ORM\Column(name="availability_comment", type="text", nullable=true)
     */
    private $availabilityComment = '';

    /**
     * @return int
     */
    public function getAvailability(): int
    {
        return $this->availability;
    }

    /**
     * @param int $availability
     */
    public function setAvailability(int $availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @return string|null
     */
    public function getAvailabilityComment(): ?string
    {
        return $this->availabilityComment;
    }

    /**
     * @param string|null $availabilityComment
     */
    public function setAvailabilityComment(?string $availabilityComment): void
    {
        $this->availabilityComment = $availabilityComment;
    }

}
