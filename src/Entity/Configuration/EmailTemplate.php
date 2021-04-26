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

namespace App\Entity\Configuration;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ozg_email_template",indexes={@ORM\Index(name="IDX_TEMPLATE_KEY", columns={"template_key"})})
 */
class EmailTemplate extends BaseEntity implements HideableEntityInterface
{
    use HideableEntityTrait;

    /**
     * @var string|null
     * @ORM\Column(name="template_key", type="string", length=255, nullable=true)
     */
    private $templateKey;

    /**
     * Description
     *
     * @var string|null
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * Sender email
     *
     * @var string|null
     * @ORM\Column(type="string", name="sender_email", length=1024, nullable=true)
     */
    protected $senderEmail;

    /**
     * Sender name
     *
     * @var string|null
     * @ORM\Column(type="string", name="sender_name", length=255, nullable=true)
     */
    protected $senderName;

    /**
     * Default recipient email
     *
     * @var string|null
     * @ORM\Column(type="string", name="default_recipient", length=1024, nullable=true)
     */
    protected $defaultRecipient;

    /**
     * Reply to email
     *
     * @var string|null
     * @ORM\Column(type="string", name="reply_to_email", length=1024, nullable=true)
     */
    protected $replyToEmail;

    /**
     * Email subject
     *
     * @var string|null
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    protected $subject;

    /**
     * Email body
     *
     * @var string|null
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    protected $body;

    /**
     * CC address list
     *
     * @var string|null
     * @ORM\Column(name="cc_addresses", type="text", nullable=true)
     */
    protected $ccAddresses;

    /**
     * EmailTemplate constructor.
     * @param string|null $templateKey
     */
    public function __construct(?string $templateKey = null)
    {
        $this->templateKey = $templateKey;
    }

    /**
     * @return string|null
     */
    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }

    /**
     * @param string|null $templateKey
     */
    public function setTemplateKey(?string $templateKey): void
    {
        $this->templateKey = $templateKey;
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
     * @return string|null
     */
    public function getSenderEmail(): ?string
    {
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
     * @return string|null
     */
    public function getSenderName(): ?string
    {
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
    public function getDefaultRecipient(): ?string
    {
        return $this->defaultRecipient;
    }

    /**
     * @param string|null $defaultRecipient
     */
    public function setDefaultRecipient(?string $defaultRecipient): void
    {
        $this->defaultRecipient = $defaultRecipient;
    }

    /**
     * @return string|null
     */
    public function getReplyToEmail(): ?string
    {
        return $this->replyToEmail;
    }

    /**
     * @param string|null $replyToEmail
     */
    public function setReplyToEmail(?string $replyToEmail): void
    {
        $this->replyToEmail = $replyToEmail;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     */
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getCcAddresses(): ?string
    {
        return $this->ccAddresses;
    }

    /**
     * @param string|null $ccAddresses
     */
    public function setCcAddresses(?string $ccAddresses): void
    {
        $addressList = explode("\n", str_replace([',', ';', '|', ' ', '|'], "\n", trim($ccAddresses)));
        $addressList = array_filter(array_map('trim', $addressList));
        $this->ccAddresses = implode(',', $addressList);
    }

    public function __toString(): string
    {
        if ($this->subject) {
            return $this->subject;
        }
        return $this->templateKey ?? 'new email template';
    }

}
