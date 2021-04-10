<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Onboarding;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\BlameableInterface;
use App\Entity\Base\BlameableTrait;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\UserAssignedEntityTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * On-boarding inquiry entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_inquiry",indexes={@ORM\Index(name="IDX_REFERENCE_KEY", columns={"reference_id", "reference_source"})})
 */
class Inquiry extends BaseEntity implements BlameableInterface, HideableEntityInterface
{
    use BlameableTrait;
    use HideableEntityTrait;
    use UserAssignedEntityTrait;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    protected $isRead = false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reference_id", type="integer", nullable=true)
     */
    protected $referenceId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference_source", type="string", length=100, nullable=true)
     */
    protected $referenceSource;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="read_at")
     */
    protected $readAt;

    /**
     * The (first) user who read the message
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true, name="user_id", onDelete="SET NULL")
     *
     * @var UserInterface|null
     */
    protected $readBy;

    /**
     * @var Inquiry|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\Inquiry", inversedBy="answers", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parent;

    /**
     * Answers for this inquiry
     *
     * @var ArrayCollection|Inquiry[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\Inquiry", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $answers;

    /**
     * Inquiry constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }

    /**
     * @return int|null
     */
    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }

    /**
     * @param int|null $referenceId
     */
    public function setReferenceId(?int $referenceId): void
    {
        $this->referenceId = $referenceId;
    }

    /**
     * @return string|null
     */
    public function getReferenceSource(): ?string
    {
        return $this->referenceSource;
    }

    /**
     * @param string|null $referenceSource
     */
    public function setReferenceSource(?string $referenceSource): void
    {
        $this->referenceSource = $referenceSource;
    }

    /**
     * @return DateTime|null
     */
    public function getReadAt(): ?DateTime
    {
        return $this->readAt;
    }

    /**
     * @param DateTime|null $readAt
     */
    public function setReadAt(?DateTime $readAt): void
    {
        $this->readAt = $readAt;
    }

    /**
     * @return UserInterface|null
     */
    public function getReadBy(): ?UserInterface
    {
        return $this->readBy;
    }

    /**
     * @param UserInterface|null $readBy
     */
    public function setReadBy(?UserInterface $readBy): void
    {
        $this->readBy = $readBy;
    }

    /**
     * @return Inquiry|null
     */
    public function getParent(): ?Inquiry
    {
        return $this->parent;
    }

    /**
     * @param Inquiry|null $parent
     */
    public function setParent(?Inquiry $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Inquiry[]|Collection
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @param Inquiry[]|Collection $answers
     */
    public function setAnswers($answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @param Inquiry $answer
     * @return self
     */
    public function addAnswer($answer): self
    {
        if (!$this->answers->contains($answer) && $answer->getId() !== $this->getId()) {
            $this->answers->add($answer);
            $answer->setParent($this);
        }

        return $this;
    }

    /**
     * @param Inquiry $answer
     * @return self
     */
    public function removeAnswer($answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            $answer->setParent($this->getParent());
        }

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $text = trim(strip_tags($this->getDescription()));
        $length = strlen($text);
        if ($length > 50) {
            $text = substr($text, 0, 50) . '...';
        } elseif (!$text) {
            $text = 'n.a.';
        }
        return $text;
    }

}
