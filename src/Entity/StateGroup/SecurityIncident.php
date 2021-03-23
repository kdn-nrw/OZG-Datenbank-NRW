<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\StateGroup;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\BlameableInterface;
use App\Entity\Base\BlameableTrait;
use App\Entity\User;
use DateTime;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class SecurityIncident
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_service_provider_security_incident")
 */
class SecurityIncident extends BaseEntity implements BlameableInterface
{
    use BlameableTrait;

    public const SUBJECT_TYPE_ATTACK = 1;
    public const SUBJECT_TYPE_LOSS = 2;
    public const SUBJECT_TYPE_THEFT = 3;
    public const SUBJECT_TYPE_VIRUS = 4;
    public const SUBJECT_TYPE_OTHER = 9;

    public const EXTENT_ONE_SYSTEM = 1;
    public const EXTENT_FEW_SYSTEMS = 2;
    public const EXTENT_ALL_SYSTEMS = 3;
    public const EXTENT_LOW_FINANCIAL_DAMAGE = 4;
    public const EXTENT_SIGNIFICANT_FINANCIAL_DAMAGE = 5;
    public const EXTENT_OTHER = 9;

    public const METHOD_VIRUS_INFECTION = 1;
    public const METHOD_SQL_INJECTION = 2;
    public const METHOD_DDOS = 3;
    public const METHOD_PHISHING_EMAIL = 4;
    public const METHOD_SYSTEM_ERROR = 5;
    public const METHOD_BREAK_IN = 6;
    public const METHOD_OTHER = 9;

    public static $subjectTypeChoices = [
        self::SUBJECT_TYPE_ATTACK => 'app.security_incident.entity.subject_type_choices.1',
        self::SUBJECT_TYPE_LOSS => 'app.security_incident.entity.subject_type_choices.2',
        self::SUBJECT_TYPE_THEFT => 'app.security_incident.entity.subject_type_choices.3',
        self::SUBJECT_TYPE_VIRUS => 'app.security_incident.entity.subject_type_choices.4',
        self::SUBJECT_TYPE_OTHER => 'app.security_incident.entity.subject_type_choices.9',
    ];

    public static $extentChoices = [
        self::EXTENT_ONE_SYSTEM => 'app.security_incident.entity.extent_choices.1',
        self::EXTENT_FEW_SYSTEMS => 'app.security_incident.entity.extent_choices.2',
        self::EXTENT_ALL_SYSTEMS => 'app.security_incident.entity.extent_choices.3',
        self::EXTENT_LOW_FINANCIAL_DAMAGE => 'app.security_incident.entity.extent_choices.4',
        self::EXTENT_SIGNIFICANT_FINANCIAL_DAMAGE => 'app.security_incident.entity.extent_choices.5',
        self::EXTENT_OTHER => 'app.security_incident.entity.extent_choices.9',
    ];

    public static $methodChoices = [
        self::METHOD_VIRUS_INFECTION => 'app.security_incident.entity.method_choices.1',
        self::METHOD_SQL_INJECTION => 'app.security_incident.entity.method_choices.2',
        self::METHOD_DDOS => 'app.security_incident.entity.method_choices.3',
        self::METHOD_PHISHING_EMAIL => 'app.security_incident.entity.method_choices.4',
        self::METHOD_SYSTEM_ERROR => 'app.security_incident.entity.method_choices.5',
        self::METHOD_BREAK_IN => 'app.security_incident.entity.method_choices.6',
        self::METHOD_OTHER => 'app.security_incident.entity.method_choices.9',
    ];

    /**
     * @var ServiceProvider
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", cascade={"persist"})
     */
    private $serviceProvider;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="occurred_on")
     */
    private $occurredOn;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="solved_on")
     */
    private $solvedOn;

    /**
     * @ORM\Column(type="integer", name="subject_type", nullable=true)
     * @var int|null
     */
    private $subjectType;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Affected
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $affected;

    /**
     * @ORM\Column(type="integer", name="extent", nullable=true)
     * @var int|null
     */
    private $extent;

    /**
     * @ORM\Column(type="integer", name="method", nullable=true)
     * @var int|null
     */
    private $method;

    /**
     * Cause
     * @var string|null
     *
     * @ORM\Column(name="cause", type="text", nullable=true)
     */
    private $cause = '';

    /**
     * Measures
     * @var string|null
     *
     * @ORM\Column(name="measures", type="text", nullable=true)
     */
    private $measures = '';

    /**
     * Informed parties
     * @var string|null
     *
     * @ORM\Column(name="informed_parties", type="text", nullable=true)
     */
    private $informedParties = '';

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function setServiceProvider($serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @return DateTime|null
     */
    public function getOccurredOn(): ?DateTime
    {
        return $this->occurredOn;
    }

    /**
     * @param DateTime|null $occurredOn
     */
    public function setOccurredOn(?DateTime $occurredOn): void
    {
        $this->occurredOn = $occurredOn;
    }

    /**
     * @return DateTime|null
     */
    public function getSolvedOn(): ?DateTime
    {
        return $this->solvedOn;
    }

    /**
     * @param DateTime|null $solvedOn
     */
    public function setSolvedOn(?DateTime $solvedOn): void
    {
        $this->solvedOn = $solvedOn;
    }

    /**
     * @return int|null
     */
    public function getSubjectType(): ?int
    {
        return $this->subjectType;
    }

    /**
     * @param int|null $subjectType
     */
    public function setSubjectType(?int $subjectType): void
    {
        $this->subjectType = $subjectType;
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
    public function getAffected(): ?string
    {
        return $this->affected;
    }

    /**
     * @param string|null $affected
     */
    public function setAffected(?string $affected): void
    {
        $this->affected = $affected;
    }

    /**
     * @return int|null
     */
    public function getExtent(): ?int
    {
        return $this->extent;
    }

    /**
     * @param int|null $extent
     */
    public function setExtent(?int $extent): void
    {
        $this->extent = $extent;
    }

    /**
     * @return int|null
     */
    public function getMethod(): ?int
    {
        return $this->method;
    }

    /**
     * @param int|null $method
     */
    public function setMethod(?int $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string|null
     */
    public function getCause(): ?string
    {
        return $this->cause;
    }

    /**
     * @param string|null $cause
     */
    public function setCause(?string $cause): void
    {
        $this->cause = $cause;
    }

    /**
     * @return string|null
     */
    public function getMeasures(): ?string
    {
        return $this->measures;
    }

    /**
     * @param string|null $measures
     */
    public function setMeasures(?string $measures): void
    {
        $this->measures = $measures;
    }

    /**
     * @return string|null
     */
    public function getInformedParties(): ?string
    {
        return $this->informedParties;
    }

    /**
     * @param string|null $informedParties
     */
    public function setInformedParties(?string $informedParties): void
    {
        $this->informedParties = $informedParties;
    }

    public function __toString(): string
    {
        $occurredOn = $this->getOccurredOn();
        if (null !== $occurredOn) {
            $text = date('d.m.Y', $occurredOn->getTimestamp());
        } else {
            $text = date('d.m.Y');
        }
        $createdBy = $this->getCreatedBy();
        if (null !== $createdBy) {
            if ($createdBy instanceof \Sonata\UserBundle\Model\User) {
                $text .= ', ' . $createdBy->getFullname();
                if ($createdBy instanceof User && null !== $organisation = $createdBy->getOrganisation()) {
                    $text .= ', ' . $organisation;
                }
                $text .= ' ('.$createdBy->getEmail().')';
            } else {
                $text .= ' ('.$createdBy.')';
            }
        }
        return $text;
    }

}
