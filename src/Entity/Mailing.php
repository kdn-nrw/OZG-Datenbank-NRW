<?php

namespace App\Entity;

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HideableEntityTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class mailing
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_mailing")
 */
class Mailing extends BaseBlamableEntity
{
    const GREETING_TYPE_NONE = 'none';
    const GREETING_TYPE_PREPEND = 'prepend';

    const STATUS_NEW = 0;
    const STATUS_PREPARED = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_FINISHED = 8;
    const STATUS_CANCELLED = 9;

    use CategoryTrait;
    use HideableEntityTrait;

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
     * @ORM\OneToMany(targetEntity="MailingContact", mappedBy="mailing", cascade={"all"})
     */
    private $mailingContacts;

    /**
     * @var MinistryState[]|Collection
     * @ORM\ManyToMany(targetEntity="MinistryState")
     * @ORM\JoinTable(name="ozg_mailing_ministry_state",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $stateMinistries;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="ServiceProvider")
     * @ORM\JoinTable(name="ozg_mailing_service_provider",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="Commune")
     * @ORM\JoinTable(name="ozg_mailing_commune",
     *     joinColumns={
     *     @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $communes;

    /**
     * @var Category[]|Collection
     * @ORM\ManyToMany(targetEntity="Category")
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
     * @ORM\ManyToMany(targetEntity="Contact")
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

    public function __construct()
    {
        $this->mailingContacts = new ArrayCollection();
        $this->stateMinistries = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
        $this->communes = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->excludeContacts = new ArrayCollection();
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject(?string $subject)
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
        }
        $this->greetingType = $greetingType;
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
    public function addMailingContact($mailingContact)
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
    public function removeMailingContact($mailingContact)
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
        return (int)$this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = (int)$status;
    }

    /**
     * Finish mailing
     */
    public function finish()
    {
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $this->setSendEndAt($now);
        $this->setStatus(Mailing::STATUS_FINISHED);
    }


    /**
     * @param MinistryState $stateMinistry
     * @return self
     */
    public function addStateMinistry($stateMinistry)
    {
        if (!$this->stateMinistries->contains($stateMinistry)) {
            $this->stateMinistries->add($stateMinistry);
        }

        return $this;
    }

    /**
     * @param MinistryState $stateMinistry
     * @return self
     */
    public function removeStateMinistry($stateMinistry)
    {
        if ($this->stateMinistries->contains($stateMinistry)) {
            $this->stateMinistries->removeElement($stateMinistry);
        }

        return $this;
    }

    /**
     * @return MinistryState[]|Collection
     */
    public function getStateMinistries()
    {
        return $this->stateMinistries;
    }

    /**
     * @param MinistryState[]|Collection $stateMinistries
     */
    public function setStateMinistries($stateMinistries): void
    {
        $this->stateMinistries = $stateMinistries;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider($serviceProvider)
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider($serviceProvider)
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $serviceProviders
     */
    public function setServiceProviders($serviceProviders): void
    {
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune)
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function removeCommune($commune)
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
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
    public function addExcludeContact($excludeContact)
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
    public function removeExcludeContact($excludeContact)
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
    public function contactIsBlacklisted(Contact $contact)
    {
        if (null === $this->blacklistEmails) {
            $this->blacklistEmails = [];
            foreach ($this->excludeContacts as $excludeContact) {
                $email = strtolower($excludeContact->getEmail());
                if (!in_array($email, $this->blacklistEmails)) {
                    $this->blacklistEmails[] = $email;
                }
            }
        }
        return in_array(strtolower($contact->getEmail()), $this->blacklistEmails);
    }

    /**
     * Update number of sent mails
     */
    public function updateSentCount()
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
