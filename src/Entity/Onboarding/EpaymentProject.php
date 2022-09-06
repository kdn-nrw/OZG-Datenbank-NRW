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
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class EpaymentProject
 *
 * @ORM\Entity()
 * @ORM\Table(name="ozg_onboarding_epayment_project")
 */
class EpaymentProject extends BaseEntity implements HideableEntityInterface, HasMetaDateEntityInterface, CalculateCompletenessEntityInterface
{
    public const PROVIDER_TYPE_GIROPAY = 'giropay';
    public const PROVIDER_TYPE_CREDIT_CARD = 'credit';
    public const PROVIDER_TYPE_PAYPAL = 'paypal';
    public const PROVIDER_TYPE_PAYDIREKT = 'paydirekt';

    public const PROJECT_ENIRONMENT_SANDBOX = 'sandbox';
    public const PROJECT_ENIRONMENT_PRODUCTION = 'production';

    /**
     * @var array Supported payment provider types
     */
    public static $providerTypeChoices = [
        'app.epayment_project.entity.provider_choices.giropay' => self::PROVIDER_TYPE_GIROPAY,
        'app.epayment_project.entity.provider_choices.credit' => self::PROVIDER_TYPE_CREDIT_CARD,
        'app.epayment_project.entity.provider_choices.paypal' => self::PROVIDER_TYPE_PAYPAL,
        'app.epayment_project.entity.provider_choices.paydirekt' => self::PROVIDER_TYPE_PAYDIREKT,
    ];

    /**
     * @var array Supported project environments
     */
    public static $projectEnvironmentChoices = [
        'app.epayment_project.entity.project_environment_choices.sandbox' => self::PROJECT_ENIRONMENT_SANDBOX,
        'app.epayment_project.entity.project_environment_choices.production' => self::PROJECT_ENIRONMENT_PRODUCTION,
    ];

    use HideableEntityTrait;

    /**
     * Commune selection type
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $providerType;

    /**
     * Project environment
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $projectEnvironment;

    /**
     * Project id
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $projectId;

    /**
     * Project id
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $projectPassword;

    /**
     * @var Epayment|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\Epayment", inversedBy="projects", cascade={"persist"})
     * @ORM\JoinColumn(name="epayment_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $epayment;

    /**
     * EpaymentProject constructor.
     * @param AbstractOnboardingEntity $onboarding
     * @param string|null $providerType
     */
    public function __construct(AbstractOnboardingEntity $onboarding, string $providerType, string $projectEnvironment)
    {
        $this->epayment = $onboarding;
        $this->providerType = $providerType;
        $this->projectEnvironment = $projectEnvironment;
    }

    /**
     * @return array
     */
    public static function getProviderTypeChoices(): array
    {
        return self::$providerTypeChoices;
    }

    /**
     * @param array $providerTypeChoices
     */
    public static function setProviderTypeChoices(array $providerTypeChoices): void
    {
        self::$providerTypeChoices = $providerTypeChoices;
    }

    /**
     * @return array
     */
    public static function getProjectEnvironmentChoices(): array
    {
        return self::$projectEnvironmentChoices;
    }

    /**
     * @param array $projectEnvironmentChoices
     */
    public static function setProjectEnvironmentChoices(array $projectEnvironmentChoices): void
    {
        self::$projectEnvironmentChoices = $projectEnvironmentChoices;
    }

    /**
     * @return string|null
     */
    public function getProviderType(): ?string
    {
        return $this->providerType;
    }

    /**
     * @return string|null
     */
    public function getProjectEnvironment(): ?string
    {
        return $this->projectEnvironment;
    }

    /**
     * @return string|null
     */
    public function getProjectId(): ?string
    {
        return $this->projectId;
    }

    /**
     * @param string|null $projectId
     */
    public function setProjectId(?string $projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string|null
     */
    public function getProjectPassword(): ?string
    {
        return $this->projectPassword;
    }

    /**
     * @param string|null $projectPassword
     */
    public function setProjectPassword(?string $projectPassword): void
    {
        $this->projectPassword = $projectPassword;
    }

    /**
     * @return Epayment|null
     */
    public function getEpayment(): ?Epayment
    {
        return $this->epayment;
    }

    /**
     * @return string
     */
    public function getLabelKey(): string
    {
        $labelKey = array_search($this->getProviderType(), self::$providerTypeChoices, false);
        if (!$labelKey) {
            $labelKey = 'app.epayment_project.entity.provider_choices.giropay';
        }
        return $labelKey;
    }

    public static function getTypeEnvironmentChoices()
    {
        $typeChoices = self::$providerTypeChoices;
        $environmentChoices = self::$projectEnvironmentChoices;
        $choices = [];
        foreach ($typeChoices as $typeKey) {
            foreach ($environmentChoices as $envKey) {
                $choices[$typeKey . '_' . $envKey] = [
                    'provider_type' => $typeKey,
                    'environment' => $envKey,
                ];
            }
        }
        return $choices;
    }

    public function getTypeEnvironmentKey(): string
    {
        return $this->getProviderType() . '_' . $this->getProjectEnvironment();
    }

    /**
     * Returns the string representation of this entity
     * @return string
     */
    public function __toString(): string
    {
        return $this->getTypeEnvironmentKey();
    }
}
