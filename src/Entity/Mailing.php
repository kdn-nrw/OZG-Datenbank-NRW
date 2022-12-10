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

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Class mailing
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_mailing")
 * @Vich\Uploadable
 */
class Mailing extends BaseBlamableEntity implements HideableEntityInterface
{
    public const GREETING_TYPE_NONE = 'none';
    public const GREETING_TYPE_PREPEND = 'prepend';

    public const STATUS_NEW = 0;
    public const STATUS_PREPARED = 1;
    public const STATUS_ACTIVE = 2;
    public const STATUS_FINISHED = 8;
    public const STATUS_CANCELLED = 9;

    public static $statusChoices = [
        Mailing::STATUS_NEW => 'app.mailing.entity.status_choices.new',
        Mailing::STATUS_PREPARED => 'app.mailing.entity.status_choices.prepared',
        Mailing::STATUS_ACTIVE => 'app.mailing.entity.status_choices.active',
        Mailing::STATUS_FINISHED => 'app.mailing.entity.status_choices.finished',
        Mailing::STATUS_CANCELLED => 'app.mailing.entity.status_choices.cancelled',
    ];

    use CategoryTrait;
    use HideableEntityTrait;

    /**
     * @ORM\Column(type="string", name="sender_name", length=255, nullable=true)
     * @var string|null
     */
    private $senderName;

    /**
     * @ORM\Column(type="string", name="email", length=255, nullable=true)
     * @var string|null
     */
    protected $senderEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $greetingType = self::GREETING_TYPE_NONE;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text_plain", type="text", nullable=true)
     */
    private $textPlain = '';

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="start_at")
     */
    protected $startAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="send_start_at")
     */
    protected $sendStartAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="send_end_at")
     */
    protected $sendEndAt;

    /**
     * @ORM\Column(type="integer", name="status")
     * @var int
     */
    private $status = self::STATUS_PREPARED;

    /**
     * @var MailingContact[]|Collection
     * @ORM\OneToMany(targetEntity="MailingContact", mappedBy="mailing", cascade={"all"}, orphanRemoval=true)
     */
    private $mailingContacts;

    /**
     * @var Organisation[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Organisation")
     * @ORM\JoinTable(name="ozg_mailing_organisation",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $organisations;

    /**
     * @var Category[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Category")
     * @ORM\JoinTable(name="ozg_mailing_category",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $categories;

    /**
     * @ORM\Column(type="integer", name="recipient_count", nullable=true)
     * @var int|null
     */
    private $recipientCount;

    /**
     * @ORM\Column(type="integer", name="sent_count", nullable=true)
     * @var int|null
     */
    private $sentCount;

    /**
     * @var Contact[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact")
     * @ORM\JoinTable(name="ozg_mailing_exclude_contact",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $excludeContacts;

    /**
     * Internal storage for contact ids; prevent adding contacts to mailing more than once
     *
     * @var int[]|null
     */
    private $contactIds;

    /**
     * @var string[]|null
     */
    private $blacklistEmails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailingAttachment", mappedBy="mailing", cascade={"persist", "remove"})
     */
    private $attachments;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->excludeContacts = new ArrayCollection();
        $this->mailingContacts = new ArrayCollection();
        $this->organisations = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getSenderName(): ?string
    {
        if (!$this->senderName) {
            $this->senderName = 'OZG Plattform';
        }
        return $this->senderName;
    }

    /**
     * @param string|null $senderName
     */
    public function setSenderName(?string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string|null
     */
    public function getSenderEmail(): ?string
    {
        if (!$this->senderEmail) {
            $this->senderEmail = 'geschaeftsstelle@kdn.de';
        }
        return $this->senderEmail;
    }

    /**
     * @param string|null $senderEmail
     */
    public function setSenderEmail(?string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Set subject
     *
     * @param string|null $subject
     * @return self
     */
    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getGreetingType(): string
    {
        if (empty($this->greetingType)) {
            $this->greetingType = self::GREETING_TYPE_NONE;
        }
        return $this->greetingType;
    }

    /**
     * @param string|null $greetingType
     */
    public function setGreetingType(?string $greetingType): void
    {
        if (empty($this->greetingType)) {
            $this->greetingType = self::GREETING_TYPE_NONE;
        } else {
            $this->greetingType = $greetingType;
        }
    }

    /**
     * @return string|null
     */
    public function getTextPlain(): ?string
    {
        return $this->textPlain;
    }

    /**
     * @param string|null $textPlain
     */
    public function setTextPlain(?string $textPlain): void
    {
        $this->textPlain = $textPlain;
    }

    /**
     * @param MailingContact $mailingContact
     * @return self
     */
    public function addMailingContact($mailingContact): self
    {
        if (!$this->mailingContacts->contains($mailingContact)) {
            $this->mailingContacts->add($mailingContact);
            $mailingContact->setMailing($this);
            if (null !== $this->contactIds) {
                $this->contactIds[] = $mailingContact->getContact()->getId();
            }
        }

        return $this;
    }

    /**
     * @param MailingContact $mailingContact
     * @return self
     */
    public function removeMailingContact($mailingContact): self
    {
        if ($this->mailingContacts->contains($mailingContact)) {
            $this->mailingContacts->removeElement($mailingContact);
        }

        return $this;
    }

    /**
     * @return MailingContact[]|Collection
     */
    public function getMailingContacts()
    {
        return $this->mailingContacts;
    }

    /**
     * @param MailingContact[]|Collection $mailingContacts
     */
    public function setMailingContacts($mailingContacts): void
    {
        $this->mailingContacts = $mailingContacts;
    }

    /**
     * @return DateTime|null
     */
    public function getStartAt(): ?DateTime
    {
        return $this->startAt;
    }

    /**
     * @param DateTime|null $startAt
     */
    public function setStartAt(?DateTime $startAt): void
    {
        $this->startAt = $startAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSendStartAt(): ?DateTime
    {
        return $this->sendStartAt;
    }

    /**
     * @param DateTime|null $sendStartAt
     */
    public function setSendStartAt(?DateTime $sendStartAt): void
    {
        $this->sendStartAt = $sendStartAt;
    }

    /**
     * @return DateTime|null
     */
    public function getSendEndAt(): ?DateTime
    {
        return $this->sendEndAt;
    }

    /**
     * @param DateTime|null $sendEndAt
     */
    public function setSendEndAt(?DateTime $sendEndAt): void
    {
        $this->sendEndAt = $sendEndAt;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = (int)$status;
    }

    /**
     * Finish mailing
     */
    public function finish(): void
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('UTC'));
        $this->setSendEndAt($now);
        $this->setStatus(self::STATUS_FINISHED);
    }


    /**
     * @param Organisation $organisation
     * @return self
     */
    public function addOrganisation($organisation): self
    {
        if (!$this->organisations->contains($organisation)) {
            $this->organisations->add($organisation);
        }

        return $this;
    }

    /**
     * @param Organisation $organisation
     * @return self
     */
    public function removeOrganisation($organisation): self
    {
        if ($this->organisations->contains($organisation)) {
            $this->organisations->removeElement($organisation);
        }

        return $this;
    }

    /**
     * @return Organisation[]|Collection
     */
    public function getOrganisations()
    {
        return $this->organisations;
    }

    /**
     * @param Organisation[]|Collection $organisations
     */
    public function setOrganisations($organisations): void
    {
        $this->organisations = $organisations;
    }

    /**
     * @return int|null
     */
    public function getRecipientCount(): ?int
    {
        return $this->recipientCount;
    }

    /**
     * @param int|null $recipientCount
     */
    public function setRecipientCount(?int $recipientCount): void
    {
        $this->recipientCount = $recipientCount;
    }

    /**
     * @return int|null
     */
    public function getSentCount(): ?int
    {
        return $this->sentCount;
    }

    /**
     * @param int|null $sentCount
     */
    public function setSentCount(?int $sentCount): void
    {
        $this->sentCount = $sentCount;
    }

    /**
     * @param Contact $excludeContact
     * @return self
     */
    public function addExcludeContact($excludeContact): self
    {
        if (!$this->excludeContacts->contains($excludeContact)) {
            $this->excludeContacts->add($excludeContact);
        }

        return $this;
    }

    /**
     * @param Contact $excludeContact
     * @return self
     */
    public function removeExcludeContact($excludeContact): self
    {
        if ($this->excludeContacts->contains($excludeContact)) {
            $this->excludeContacts->removeElement($excludeContact);
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getExcludeContacts()
    {
        return $this->excludeContacts;
    }

    /**
     * @param Contact[]|Collection $excludeContacts
     */
    public function setExcludeContacts($excludeContacts): void
    {
        $this->excludeContacts = $excludeContacts;
    }

    /**
     * Returns true if given contact is blacklisted for this mailing; false otherwise
     *
     * @param Contact $contact
     * @return bool
     */
    public function contactIsBlacklisted(Contact $contact): bool
    {
        if (null === $this->blacklistEmails) {
            $this->blacklistEmails = [];
            foreach ($this->excludeContacts as $excludeContact) {
                $email = strtolower($excludeContact->getEmail());
                if (!in_array($email, $this->blacklistEmails, false)) {
                    $this->blacklistEmails[] = $email;
                }
            }
        }
        return in_array(strtolower($contact->getEmail()), $this->blacklistEmails, false);
    }

    /**
     * Update number of sent mails
     */
    public function updateSentCount(): void
    {
        $sentCount = 0;
        $mailingContacts = $this->getMailingContacts();
        foreach ($mailingContacts as $mailingContact) {
            if (!$mailingContact->isHidden() && $mailingContact->getSendStatus() === self::STATUS_FINISHED) {
                ++$sentCount;
            }
        }
        $this->setSentCount(min($sentCount, count($mailingContacts)));
    }

    /**
     * @return int[]|null
     */
    public function getContactIds(): ?array
    {
        if (null === $this->contactIds) {
            $this->contactIds = [];
            $mailingContacts = $this->getMailingContacts();
            $mailingIsActive = $this->getStatus() === self::STATUS_PREPARED || $this->getStatus() === self::STATUS_ACTIVE;
            foreach ($mailingContacts as $child) {
                if ($mailingIsActive && $child->getSendStatus() === self::STATUS_NEW) {
                    $child->setSendStatus(self::STATUS_PREPARED);
                }
                if (null !== $mc = $child->getContact()) {
                    $this->contactIds[] = $mc->getId();
                }
            }
        }
        return $this->contactIds;
    }

    /**
     * Add attachment
     *
     * @param MailingAttachment $attachment
     *
     * @return self
     */
    public function addAttachment(MailingAttachment $attachment): self
    {
        $this->attachments->add($attachment);
        $attachment->setMailing($this);
        // $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * Remove attachment
     *
     * @param MailingAttachment $attachment
     */
    public function removeAttachment(MailingAttachment $attachment): void
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param MailingAttachment[]|Collection $attachments
     */
    public function setAttachments($attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * Mailing to string
     *
     * @return string
     */
    public function __toString(): string
    {
        $text = '';
        $createdBy = $this->getCreatedBy();
        if (null !== $createdBy) {
            /** @var User $createdBy */
            $text .= $createdBy->getFullname() . ', ';
        }
        $createdAt = $this->getCreatedAt();
        if (null !== $createdAt) {
            $text .= $createdAt->format('d.m.Y H:i:s') . ':';
        }
        $subject = $this->getSubject();
        if (!empty($subject)) {
            $text .= $subject;
        } else {
            $text .= ' Kein Betreff';
        }
        return $text;
    }
}
