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

use App\Entity\Base\BaseEntity;
use App\Entity\Base\HideableEntityTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class mailing contact
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_mailing_contact")
 */
class MailingContact extends BaseEntity
{
    use HideableEntityTrait;
    use ContactEntityTrait;

    const SEND_STATUS_FAILED = 3;
    const SEND_STATUS_DISABLED = 5;

    /**
     * @var Mailing
     * @ORM\ManyToOne(targetEntity="Mailing", inversedBy="mailingContacts", cascade={"persist"})
     * @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $mailing;

    /**
     * @var Contact
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contact;

    /**
     * @ORM\Column(type="integer", name="send_status")
     * @var int
     */
    private $sendStatus = Mailing::STATUS_NEW;

    /**
     * @ORM\Column(type="integer", name="send_attempts")
     * @var int
     */
    private $sendAttempts = 0;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="sent_at")
     */
    protected $sentAt;

    /**
     * @return Mailing
     */
    public function getMailing()
    {
        return $this->mailing;
    }

    /**
     * @param Mailing $mailing
     */
    public function setMailing($mailing): void
    {
        $this->mailing = $mailing;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact($contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return int
     */
    public function getSendStatus(): int
    {
        return (int)$this->sendStatus;
    }

    /**
     * @param int $sendStatus
     */
    public function setSendStatus(?int $sendStatus): void
    {
        $this->sendStatus = (int)$sendStatus;
    }

    public function getSendStatusLabel()
    {
        switch ($this->getSendStatus()) {
            case Mailing::STATUS_PREPARED:
                $label = 'app.mailing.entity.status_choices.prepared';
                break;
            case Mailing::STATUS_ACTIVE:
                $label = 'app.mailing.entity.status_choices.active';
                break;
            case Mailing::STATUS_FINISHED:
                $label = 'app.mailing.entity.status_choices.finished';
                break;
            case Mailing::STATUS_CANCELLED:
                $label = 'app.mailing.entity.status_choices.cancelled';
                break;
            case self::SEND_STATUS_FAILED:
                $label = 'app.mailing_contact.entity.status_choices.failed';
                break;
            case self::SEND_STATUS_DISABLED:
                $label = 'app.mailing_contact.entity.status_choices.disabled';
                break;
            //case Mailing::STATUS_NEW:
            default:
                $label = 'app.mailing.entity.status_choices.new';
                break;
        }
        return $label;
    }

    /**
     * @return DateTime|null
     */
    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    /**
     * @return int
     */
    public function getSendAttempts(): int
    {
        return (int) $this->sendAttempts;
    }

    /**
     * @param int $sendAttempts
     */
    public function setSendAttempts(?int $sendAttempts): void
    {
        $this->sendAttempts = (int) $sendAttempts;
    }

    public function incrementSendAttempts()
    {
        $sendAttempts = $this->getSendAttempts() + 1;
        $this->setSendAttempts($sendAttempts);
        if ($sendAttempts > 3) {
            $this->setSendStatus(self::SEND_STATUS_FAILED);
        }
    }

    /**
     * @param DateTime|null $sentAt
     */
    public function setSentAt(?DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    /**
     * Finish mailing
     */
    public function finish()
    {
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $this->setSentAt($now);
        $this->setSendStatus(Mailing::STATUS_FINISHED);
    }

    /**
     * Mailing contact to string
     *
     * @return string
     */
    public function __toString(): string
    {
        $contact = $this->getContact();
        if (null !== $contact) {
            return $contact->getDisplayName();
        }
        return 'n.a.';
    }

}
