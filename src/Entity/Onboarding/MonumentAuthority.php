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

namespace App\Entity\Onboarding;

use App\Entity\StateGroup\CommuneType;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class MonumentAuthority
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_monument_authority")
 */
class MonumentAuthority extends AbstractOnboardingEntity
{
    public const DOCUMENT_TYPE_PRIVATE_KEY = 'osci_private_key_file';
    public const DOCUMENT_TYPE_PUBLIC_KEY = 'osci_public_key_file';

    public const REQUIRED_COMMUNE_BUREAU_ID = 71;

    public const APPLICATION_TYPE_NEW = 1;
    public const APPLICATION_TYPE_UPDATE = 2;

    public const INTERMEDIARY_OPERATOR_TYPE_1 = 1;
    public const INTERMEDIARY_OPERATOR_TYPE_2 = 2;
    public const INTERMEDIARY_OPERATOR_TYPE_UNKNOWN = 9;

    /**
     * @var array application type choices
     */
    public static $applicationTypeChoices = [
        'app.monument_authority.entity.application_type_choices.new' => self::APPLICATION_TYPE_NEW,
        'app.monument_authority.entity.application_type_choices.update' => self::APPLICATION_TYPE_UPDATE,
    ];
    /**
     * @var array intermediary operator type choices
     */
    public static $intermediaryOperatorTypeChoices = [
        'app.monument_authority.entity.intermediary_operator_type_choices.1' => self::INTERMEDIARY_OPERATOR_TYPE_1,
        'app.monument_authority.entity.intermediary_operator_type_choices.2' => self::INTERMEDIARY_OPERATOR_TYPE_2,
        'app.monument_authority.entity.intermediary_operator_type_choices.9' => self::INTERMEDIARY_OPERATOR_TYPE_UNKNOWN,
    ];

    /**
     * Application type
     * @var int|null
     *
     * @ORM\Column(name="application_type", type="integer", nullable=true)
     */
    protected $applicationType;

    /**
     * Intermediary operator type
     * @var int|null
     *
     * @ORM\Column(name="intermediary_operator_type", type="integer", nullable=true)
     */
    protected $intermediaryOperatorType;

    /**
     * @ORM\Column(type="string", name="state", length=100, nullable=true)
     * @var string|null
     */
    protected $state = 'Nordrhein-Westfalen';

    /**
     * @ORM\Column(type="string", name="authority_category", length=100, nullable=true)
     * @var string|null
     */
    protected $authorityCategory = 'Bauaufsichtsbehörde';

    /**
     * @ORM\Column(type="string", name="organizational_key", length=100, nullable=true)
     * @var string|null
     */
    protected $organizationalKey;

    /**
     * Contact for this entity
     *
     * @var Contact|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\Contact", mappedBy="monumentAuthority", cascade={"all"})
     */
    protected $contact;

    /**
     * Comment
     *
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    protected $comment = '';

    /**
     * List of document types that must always be defined
     * @see getDocuments
     * @var array
     */
    protected $requiredDocumentTypes = [
        self::DOCUMENT_TYPE_PUBLIC_KEY,
        self::DOCUMENT_TYPE_PRIVATE_KEY,
    ];

    /**
     * osci private key password
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $osciPrivateKeyPassword;

    /**
     * @return int|null
     */
    public function getApplicationType(): ?int
    {
        return $this->applicationType;
    }

    /**
     * @param int|null $applicationType
     */
    public function setApplicationType(?int $applicationType): void
    {
        $this->applicationType = $applicationType;
    }

    /**
     * @return string|null
     */
    public function getOrganizationalKey(): ?string
    {
        if (!$this->organizationalKey && null !== $this->comment) {
            $this->organizationalKey = 'bab:' . $this->commune->getOfficialCommunityKey();
        }
        return $this->organizationalKey;
    }

    /**
     * @param string|null $organizationalKey
     */
    public function setOrganizationalKey(?string $organizationalKey): void
    {
        $this->organizationalKey = $organizationalKey;
    }

    /**
     * @return int|null
     */
    public function getIntermediaryOperatorType(): ?int
    {
        return $this->intermediaryOperatorType;
    }

    /**
     * @param int|null $intermediaryOperatorType
     */
    public function setIntermediaryOperatorType(?int $intermediaryOperatorType): void
    {
        $this->intermediaryOperatorType = $intermediaryOperatorType;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getAuthorityCategory(): ?string
    {
        return $this->authorityCategory;
    }

    /**
     * @param string|null $authorityCategory
     */
    public function setAuthorityCategory(?string $authorityCategory): void
    {
        $this->authorityCategory = $authorityCategory;
    }

    /**
     * Return the contact
     * @return Contact
     */
    public function getContact(): Contact
    {
        if (null === $this->contact) {
            $this->contact = new Contact($this, Contact::CONTACT_TYPE_MONUMENT_AUTHORITY);
        }
        return $this->contact;
    }

    /**
     * Sets the contact
     * @param Contact|null $contact
     */
    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }


    /**
     * @return string|null
     */
    public function getOsciPrivateKeyPassword(): ?string
    {
        return $this->osciPrivateKeyPassword;
    }

    /**
     * @param string|null $osciPrivateKeyPassword
     */
    public function setOsciPrivateKeyPassword(?string $osciPrivateKeyPassword): void
    {
        $this->osciPrivateKeyPassword = $osciPrivateKeyPassword;
    }

    /**
     * @return string
     */
    public function getApplicationTypeLabelKey(): string
    {
        $choiceValue = $this->getApplicationType();
        if (null !== $choiceValue && $key = array_search($choiceValue, self::$applicationTypeChoices, true)) {
            return $key;
        }
        return array_search(self::APPLICATION_TYPE_NEW, self::$applicationTypeChoices, true);
    }


    /**
     * @return string
     */
    public function getIntermediaryOperatorTypeLabelKey(): string
    {
        $choiceValue = $this->getIntermediaryOperatorType();
        if (null !== $choiceValue && $key = array_search($choiceValue, self::$intermediaryOperatorTypeChoices, true)) {
            return $key;
        }
        return array_search(self::APPLICATION_TYPE_NEW, self::$intermediaryOperatorTypeChoices, true);
    }

    /**
     * @return CommuneType|null
     */
    public function getCommuneType(): ?CommuneType
    {
        if (null !== $this->commune) {
            return $this->commune->getCommuneType();
        }
        return null;
    }
}
