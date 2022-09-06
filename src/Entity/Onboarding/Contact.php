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

use App\Entity\AddressTrait;
use App\Entity\Base\BaseEntity;
use App\Entity\Base\ContactPropertiesTrait;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\PersonInterface;
use App\Entity\Base\PersonPropertiesTrait;
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\StateGroup\Commune;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Contact
 *
 * @ORM\Entity()
 * @ORM\Table(name="ozg_onboarding_contact")
 */
class Contact extends BaseEntity implements HideableEntityInterface, PersonInterface, HasMetaDateEntityInterface, CalculateCompletenessEntityInterface
{
    public const CONTACT_TYPE_BIS = 'bis';
    public const CONTACT_TYPE_FS = 'fs';
    public const CONTACT_TYPE_EPAY = 'epay';
    public const CONTACT_TYPE_SUPER = 'super';
    public const CONTACT_TYPE_CLERK = 'clerk';
    public const CONTACT_TYPE_WEB = 'web';

    public const CONTACT_TYPE_EPAYMENT_USER = 'epay_user';
    public const CONTACT_TYPE_XTA = 'xta_server_contact';
    public const CONTACT_TYPE_MONUMENT_AUTHORITY = 'monument_authority_contact';

    /**
     * @var array Supported contact types
     */
    public static $contactTypeChoices = [
        'app.commune_info.entity.contact_type_choices.bis' => self::CONTACT_TYPE_BIS,
        'app.commune_info.entity.contact_type_choices.fs' => self::CONTACT_TYPE_FS,
        'app.commune_info.entity.contact_type_choices.epay' => self::CONTACT_TYPE_EPAY,
        'app.commune_info.entity.contact_type_choices.super' => self::CONTACT_TYPE_SUPER,
        'app.commune_info.entity.contact_type_choices.clerk' => self::CONTACT_TYPE_CLERK,
        'app.commune_info.entity.contact_type_choices.web' => self::CONTACT_TYPE_WEB,
    ];

    /**
     * @var Commune|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $commune;

    /**
     * @var CommuneInfo|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\CommuneInfo", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_info_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $communeInfo;

    /**
     * @var Epayment|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\Epayment", inversedBy="paymentUser", cascade={"persist"})
     * @ORM\JoinColumn(name="epayment_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $epayment;

    /**
     * @var FormSolution|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\FormSolution", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinColumn(name="form_solution_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $formSolution;

    /**
     * @var ServiceAccount|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\ServiceAccount", inversedBy="paymentUser", cascade={"persist"})
     * @ORM\JoinColumn(name="service_account_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $serviceAccount;

    /**
     * @var XtaServer|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\XtaServer", inversedBy="contact", cascade={"persist"})
     * @ORM\JoinColumn(name="xta_server_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $xtaServer;

    /**
     * @var MonumentAuthority|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\MonumentAuthority", inversedBy="contact", cascade={"persist"})
     * @ORM\JoinColumn(name="monument_authority_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $monumentAuthority;

    use PersonPropertiesTrait;
    use ContactPropertiesTrait;

    use HideableEntityTrait;
    use AddressTrait;

    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $contactType;

    /**
     * External user name
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $externalUserName;

    /**
     * Contact constructor.
     * @param AbstractOnboardingEntity $onboarding
     * @param string|null $contactType
     */
    public function __construct(AbstractOnboardingEntity $onboarding, string $contactType)
    {
        $this->contactType = $contactType;
        if ($onboarding instanceof CommuneInfo) {
            $this->communeInfo = $onboarding;
        } elseif ($onboarding instanceof Epayment) {
            $this->epayment = $onboarding;
        } elseif ($onboarding instanceof ServiceAccount) {
            $this->serviceAccount = $onboarding;
        } elseif ($onboarding instanceof FormSolution) {
            $this->formSolution = $onboarding;
        } elseif ($onboarding instanceof XtaServer) {
            $this->xtaServer = $onboarding;
        } elseif ($onboarding instanceof MonumentAuthority) {
            $this->monumentAuthority = $onboarding;
        }
        $this->commune = $onboarding->getCommune();
    }

    /**
     * @return ?object
     */
    public function getOnboardingEntity(): ?object
    {
        if (null !== $this->communeInfo) {
            return $this->communeInfo;
        }
        if (null !== $this->epayment) {
            return $this->epayment;
        }
        if (null !== $this->serviceAccount) {
            return $this->serviceAccount;
        }
        if (null !== $this->formSolution) {
            return $this->formSolution;
        }
        if (null !== $this->xtaServer) {
            return $this->xtaServer;
        }
        if (null !== $this->monumentAuthority) {
            return $this->monumentAuthority;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getContactType(): string
    {
        return $this->contactType;
    }

    /**
     * @param string $contactType
     */
    public function setContactType(string $contactType): void
    {
        $this->contactType = $contactType;
    }

    public function getDisplayName(): string
    {
        return $this->getFullName();
    }

    /**
     * @return Commune|null
     */
    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    /**
     * Returns the name of this contact
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    /**
     * @return string|null
     */
    public function getExternalUserName(): ?string
    {
        return $this->externalUserName;
    }

    /**
     * @param string|null $externalUserName
     */
    public function setExternalUserName(?string $externalUserName): void
    {
        $this->externalUserName = $externalUserName;
    }

    /**
     * @return string
     */
    public function getLabelKey(): string
    {
        $labelKey = array_search($this->getContactType(), self::$contactTypeChoices, false);
        if (!$labelKey) {
            $labelKey = 'app.commune_info.entity.contact_default';
        }
        return $labelKey;
    }

    /**
     * @return CommuneInfo|null
     */
    public function getCommuneInfo(): ?CommuneInfo
    {
        return $this->communeInfo;
    }

    /**
     * @return Epayment|null
     */
    public function getEpayment(): ?Epayment
    {
        return $this->epayment;
    }

    /**
     * @return ServiceAccount|null
     */
    public function getServiceAccount(): ?ServiceAccount
    {
        return $this->serviceAccount;
    }

    /**
     * @return XtaServer|null
     */
    public function getXtaServer(): ?XtaServer
    {
        return $this->xtaServer;
    }

    /**
     * @return MonumentAuthority|null
     */
    public function getMonumentAuthority(): ?MonumentAuthority
    {
        return $this->monumentAuthority;
    }

}
