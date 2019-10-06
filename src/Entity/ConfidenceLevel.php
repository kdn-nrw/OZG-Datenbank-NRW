<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class Vertrauensniveau
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_confidence_level")
 * @ORM\HasLifecycleCallbacks
 */
class ConfidenceLevel extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;

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
