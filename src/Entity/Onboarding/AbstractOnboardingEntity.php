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
use App\Entity\Base\HasDocumentsEntityInterface;
use App\Entity\Base\HasGroupEmailEntityInterface;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Configuration\CustomValue;
use App\Entity\Configuration\CustomValuesCollectionAggregateTrait;
use App\Entity\Configuration\HasCustomFieldsEntityInterface;
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SimpleThings\EntityAudit\Collection\AuditedCollection;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;


/**
 * Abstract on-boarding entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="record_type", type="string")
 * @DiscriminatorMap(typeProperty="record_type", mapping={
 *    "communeinfo"="App\Entity\Onboarding\CommuneInfo",
 *    "epayment"="App\Entity\Onboarding\Epayment",
 *    "formsolution"="App\Entity\Onboarding\FormSolution",
 *    "release"="App\Entity\Onboarding\Release",
 *    "serviceaccount"="App\Entity\Onboarding\ServiceAccount",
 *    "xtaserver"="App\Entity\Onboarding\XtaServer",
 *    "monumentauthority"="App\Entity\Onboarding\MonumentAuthority"
 * })
 */
abstract class AbstractOnboardingEntity extends BaseEntity implements
    BlameableInterface,
    HideableEntityInterface,
    HasCustomFieldsEntityInterface,
    HasDocumentsEntityInterface,
    HasMetaDateEntityInterface,
    HasGroupEmailEntityInterface,
    CalculateCompletenessEntityInterface
{
    public const DEFAULT_RECIPIENT_DATA_COMPLETE = 'kommunalportal@kdn.de';

    use BlameableTrait;
    use HideableEntityTrait;
    use CustomValuesCollectionAggregateTrait;

    /**
     * @var Commune
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $commune;

    /**
     * @var string|null
     */
    protected $communeName;

    /**
     * @var string|null
     */
    protected $officialCommuneKey;

    /**
     * @var ServiceProvider|null
     * @ORM\OneToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $serviceProvider;

    /**
     * @ORM\Column(type="string", name="group_email", length=255, nullable=true)
     * @var string|null
     */
    protected $groupEmail;

    public const STATUS_NEW = 0;
    public const STATUS_INCOMPLETE = 2;
    public const STATUS_COMPLETE = 9;
    public const STATUS_COMPLETE_CONFIRMED = 10;

    public static $statusChoices = [
        AbstractOnboardingEntity::STATUS_NEW => 'app.abstract_onboarding_entity.entity.status_choices.new',
        AbstractOnboardingEntity::STATUS_INCOMPLETE => 'app.abstract_onboarding_entity.entity.status_choices.incomplete',
        AbstractOnboardingEntity::STATUS_COMPLETE => 'app.abstract_onboarding_entity.entity.status_choices.complete',
        AbstractOnboardingEntity::STATUS_COMPLETE_CONFIRMED => 'app.abstract_onboarding_entity.entity.status_choices.complete_confirmed',
    ];

    /**
     * @ORM\Column(type="integer", name="status")
     * @var int
     */
    protected $status = self::STATUS_NEW;

    /**
     * Completion rate
     *
     * @var int
     *
     * @ORM\Column(name="completion_rate", type="integer", nullable=true)
     */
    protected $completionRate = 0;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * Custom values for this entity
     *
     * @var ArrayCollection|OnboardingCustomValue[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\OnboardingCustomValue", mappedBy="onboarding")
     */
    protected $customValues;

    /**
     * @ORM\Column(name="message_count", type="integer", nullable=true)
     * @var int|null
     */
    protected $messageCount = 0;

    /**
     * The completeness of the data has been confirmed
     *
     * @var bool
     *
     * @ORM\Column(name="data_completeness_confirmed", type="boolean", nullable=true)
     */
    protected $dataCompletenessConfirmed = false;

    /**
     * @var OnboardingDocument[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\OnboardingDocument", mappedBy="onboarding", cascade={"persist", "remove"})
     */
    protected $documents;

    /**
     * List of document types that must always be defined
     * @see getDocuments
     * @var array
     */
    protected $requiredDocumentTypes = [];

    public function getName(): ?string
    {
        return $this->getCommuneName();
    }

    public function setName(?string $name)
    {
        $this->setCommuneName($name);
    }

    public function __construct(Commune $commune)
    {
        $this->commune = $commune;
        $this->communeName = $commune->getName();
        $this->documents = new ArrayCollection();
        $this->officialCommuneKey = $commune->getOfficialCommunityKey();
        $this->customValues = new ArrayCollection();
    }

    /**
     * @return Commune
     */
    public function getCommune(): Commune
    {
        return $this->commune;
    }

    /**
     * @param Commune $commune
     */
    public function setCommune(Commune $commune): void
    {
        $this->commune = $commune;
    }

    /**
     * @return string|null
     */
    public function getCommuneName(): ?string
    {
        if (!$this->communeName) {
            return $this->getCommune()->getName();
        }
        return $this->communeName;
    }

    /**
     * @param string|null $communeName
     */
    public function setCommuneName(?string $communeName): void
    {
        $this->communeName = $communeName;
    }

    /**
     * @return string|null
     */
    public function getOfficialCommuneKey(): ?string
    {
        if (!$this->officialCommuneKey) {
            return $this->getCommune()->getOfficialCommunityKey();
        }
        return $this->officialCommuneKey;
    }

    /**
     * @param string|null $officialCommuneKey
     */
    public function setOfficialCommuneKey(?string $officialCommuneKey): void
    {
        $this->officialCommuneKey = $officialCommuneKey;
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider|null $serviceProvider
     */
    public function setServiceProvider(?ServiceProvider $serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status ?? self::STATUS_NEW;
    }

    /**
     * @param int|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = (int)$status;
    }

    /**
     * @param CustomValue $customValue
     * @return void
     */
    public function addCustomValue(CustomValue $customValue): void
    {
        $customValues = $this->getCustomValues();
        if (!$customValues->contains($customValue)) {
            if ($customValue instanceof OnboardingCustomValue) {
                $customValue->setOnboarding($this);
            }
            $customValues->add($customValue);
        }
    }

    /**
     * @return int
     */
    public function getCompletionRate(): int
    {
        return (int) $this->completionRate;
    }

    /**
     * @param int $completionRate
     */
    public function setCompletionRate(int $completionRate): void
    {
        $this->completionRate = (int) max(0, min($completionRate, 100));
        if ($this->completionRate === 100) {
            if ($this->status !== self::STATUS_COMPLETE_CONFIRMED) {
                $this->setStatus(self::STATUS_COMPLETE);
            }
        } elseif ($this->completionRate > 0) {
            $this->setDataCompletenessConfirmed(false);
            $this->setStatus(self::STATUS_INCOMPLETE);
        } else {
            $this->setDataCompletenessConfirmed(false);
            $this->setStatus(self::STATUS_NEW);
        }
    }

    /**
     * @return string
     */
    public function getStatusLabelKey(): string
    {
        $status = $this->getStatus();
        if (array_key_exists($status, self::$statusChoices)) {
            return self::$statusChoices[$status];
        }
        return self::$statusChoices[self::STATUS_NEW];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $commune = $this->getCommune();
        $serviceProvider = $this->getServiceProvider();
        $string = $commune . '';
        if (null !== $serviceProvider) {
            if (empty($string)) {
                $string = (string) $serviceProvider;
            } else {
                $string .= ' / ' . $serviceProvider;
            }
        }
        if (empty($string)) {
            return 'n/a';
        }
        return $string;
    }

    /**
     * @return int
     */
    public function getMessageCount(): int
    {
        return (int) $this->messageCount;
    }

    /**
     * @param int|null $messageCount
     */
    public function setMessageCount(?int $messageCount): void
    {
        $this->messageCount = (int) $messageCount;
    }

    /**
     * @return bool
     */
    public function isDataCompletenessConfirmed(): bool
    {
        return (bool) $this->dataCompletenessConfirmed;
    }

    /**
     * @param bool $dataCompletenessConfirmed
     */
    public function setDataCompletenessConfirmed(bool $dataCompletenessConfirmed): void
    {
        $this->dataCompletenessConfirmed = $dataCompletenessConfirmed;
    }

    /**
     * @param bool $fallbackToDefault
     * @return string|null
     */
    public function getGroupEmail(bool $fallbackToDefault = false): ?string
    {
        if ($fallbackToDefault && null === $this->groupEmail && $mainCommuneEmail = $this->commune->getMainEmail()) {
            $mailParts = explode('@', $mainCommuneEmail);
            return 'epaybl@' . $mailParts[1];
        }
        return $this->groupEmail;
    }

    /**
     * @param string|null $groupEmail
     */
    public function setGroupEmail(?string $groupEmail): void
    {
        $this->groupEmail = $groupEmail;
    }

    /**
     * Add document
     *
     * @param OnboardingDocument $document
     *
     * @return self
     */
    public function addDocument(OnboardingDocument $document): self
    {
        $this->documents->add($document);
        $document->setOnboarding($this);
        return $this;
    }

    /**
     * Remove document
     *
     * @param OnboardingDocument $document
     */
    public function removeDocument(OnboardingDocument $document): void
    {
        $this->documents->removeElement($document);
        $document->setOnboarding(null);
    }

    /**
     * Get documents
     *
     * @return OnboardingDocument[]|ArrayCollection
     */
    public function getDocuments(): Collection
    {
        $collection = $this->documents;
        // Prevent exception when loading audits; only used by AuditReader
        if ($collection instanceof AuditedCollection) {
            return $collection;
        }
        if ($collection instanceof Collection && !empty($this->requiredDocumentTypes)) {
            /** @var OnboardingDocument[]|ArrayCollection $collection */
            $typeChoices = $this->requiredDocumentTypes;
            $order = [];
            $sorting = 1;
            $missingTypes = [];
            foreach ($typeChoices as $typeKey) {
                $order[$typeKey] = $sorting;
                $missingTypes[$typeKey] = true;
                ++$sorting;
            }
            foreach ($collection as $entity) {
                $missingTypes[$entity->getDocumentType()] = false;
            }
            foreach ($missingTypes as $typeKey => $isMissing) {
                if ($isMissing) {
                    $collection->add(new OnboardingDocument($this, $typeKey));
                }
            }
            /** @var ArrayCollection $collection */
            $iterator = $collection->getIterator();
            $iterator->uasort(static function (OnboardingDocument $a, OnboardingDocument $b) use ($order) {
                $orderA = $order[$a->getDocumentType()] ?? 999;
                $orderB = $order[$b->getDocumentType()] ?? 999;
                return ($orderA < $orderB) ? -1 : 1;
            });
            /** @var OnboardingDocument[] $sortedEntities */
            $sortedEntities = iterator_to_array($iterator);
            $collection->clear();
            $addedTypes = [];
            foreach ($sortedEntities as $collectionEntity) {
                $documentType = $collectionEntity->getDocumentType();
                // All document types except the default one can only be used once
                if ($documentType !== OnboardingDocument::DOCUMENT_TYPE_GENERAL && isset($addedTypes[$documentType])) {
                    continue;
                }
                $collection->add($collectionEntity);
                $addedTypes[$collectionEntity->getDocumentType()] = true;
            }
        }
        return $collection;
    }

    /**
     * @param OnboardingDocument[]|Collection $documents
     */
    public function setDocuments($documents): void
    {
        $this->documents = $documents;
    }

    /**
     * Hook on persist and update operations.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return OnboardingDocument[]|array Invalid documents (without file reference)
     */
    public function cleanDocuments(): array
    {
        $removeDocuments = [];
        foreach ($this->documents as $document) {
            /** @var OnboardingDocument $document */
            if (0 < (int)$document->getId() && null === $document->getLocalName()) {
                $removeDocuments[] = $document;
            }
        }
        foreach ($removeDocuments as $document) {
            $this->removeDocument($document);
        }
        return $removeDocuments;
    }
}
