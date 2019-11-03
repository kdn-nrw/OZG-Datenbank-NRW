<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Class Import
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_import")
 * @ORM\HasLifecycleCallbacks
 */
class Import extends BaseEntity
{
    /**
     * Themenfeld
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * Subject import id
     * @var int|null
     *
     * @ORM\Column(name="subject_import_ud", type="integer", nullable=true)
     */
    private $subjectImportId = 0;

    /**
     * Lage
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $situation;

    /**
     * situation import id
     * @var int|null
     *
     * @ORM\Column(name="situation_import_ud", type="integer", nullable=true)
     */
    private $situationImportId = 0;

    /**
     * OZG-Leistungsbündel
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceSystem;

    /**
     * Service system import id
     * @var int|null
     *
     * @ORM\Column(name="service_system_import_ud", type="integer", nullable=true)
     */
    private $serviceSystemImportId = 0;

    /**
     * OZG-ID
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceSystemId;

    /**
     * LeiKa-Bezeichnung
     * @var string|null
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $service;

    /**
     * LeiKa-Schlüssel
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceKey;

    /**
     * LeiKa-Typ
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serviceType;

    /**
     * Rechtsgrundlage(n)
     * @var string|null
     *
     * @ORM\Column(name="legal_basis", type="text", nullable=true)
     */
    private $legalBasis = '';

    /**
     * Gesetz(e)
     * @var string|null
     *
     * @ORM\Column(name="laws", type="text", nullable=true)
     */
    private $laws = '';

    /**
     * Gesetzeskürzel
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lawShortcuts;

    /**
     * SDG1-Relevanz
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $relevance1;

    /**
     * SDG2-Relevanz
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $relevance2;

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
    public function getSituation(): ?string
    {
        return $this->situation;
    }

    /**
     * @param string|null $situation
     */
    public function setSituation(?string $situation): void
    {
        $this->situation = $situation;
    }

    /**
     * @return string|null
     */
    public function getServiceSystem(): ?string
    {
        return $this->serviceSystem;
    }

    /**
     * @param string|null $serviceSystem
     */
    public function setServiceSystem(?string $serviceSystem): void
    {
        $this->serviceSystem = $serviceSystem;
    }

    /**
     * @return string|null
     */
    public function getServiceSystemId(): ?string
    {
        return $this->serviceSystemId;
    }

    /**
     * @param string|null $serviceSystemId
     */
    public function setServiceSystemId(?string $serviceSystemId): void
    {
        $this->serviceSystemId = $serviceSystemId;
    }

    /**
     * @return string|null
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * @param string|null $service
     */
    public function setService(?string $service): void
    {
        $this->service = $service;
    }

    /**
     * @return string|null
     */
    public function getServiceId(): ?string
    {
        return $this->serviceId;
    }

    /**
     * @param string|null $serviceId
     */
    public function setServiceId(?string $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return string|null
     */
    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    /**
     * @param string|null $serviceType
     */
    public function setServiceType(?string $serviceType): void
    {
        $this->serviceType = $serviceType;
    }

    /**
     * @return string|null
     */
    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    /**
     * @param string|null $legalBasis
     */
    public function setLegalBasis(?string $legalBasis): void
    {
        $this->legalBasis = $legalBasis;
    }

    /**
     * @return string|null
     */
    public function getLaws(): ?string
    {
        return $this->laws;
    }

    /**
     * @param string|null $laws
     */
    public function setLaws(?string $laws): void
    {
        $this->laws = $laws;
    }

    /**
     * @return string|null
     */
    public function getLawShortcuts(): ?string
    {
        return $this->lawShortcuts;
    }

    /**
     * @param string|null $lawShortcuts
     */
    public function setLawShortcuts(?string $lawShortcuts): void
    {
        $this->lawShortcuts = $lawShortcuts;
    }

    /**
     * @return string|null
     */
    public function getRelevance1(): ?string
    {
        return $this->relevance1;
    }

    /**
     * @param string|null $relevance1
     */
    public function setRelevance1(?string $relevance1): void
    {
        $this->relevance1 = $relevance1;
    }

    /**
     * @return string|null
     */
    public function getRelevance2(): ?string
    {
        return $this->relevance2;
    }

    /**
     * @param string|null $relevance2
     */
    public function setRelevance2(?string $relevance2): void
    {
        $this->relevance2 = $relevance2;
    }

    /**
     * @return int|null
     */
    public function getSubjectImportId(): ?int
    {
        return $this->subjectImportId;
    }

    /**
     * @param int|null $subjectImportId
     */
    public function setSubjectImportId(?int $subjectImportId): void
    {
        $this->subjectImportId = $subjectImportId;
    }

    /**
     * @return int|null
     */
    public function getSituationImportId(): ?int
    {
        return $this->situationImportId;
    }

    /**
     * @param int|null $situationImportId
     */
    public function setSituationImportId(?int $situationImportId): void
    {
        $this->situationImportId = $situationImportId;
    }

    /**
     * @return int|null
     */
    public function getServiceSystemImportId(): ?int
    {
        return $this->serviceSystemImportId;
    }

    /**
     * @param int|null $serviceSystemImportId
     */
    public function setServiceSystemImportId(?int $serviceSystemImportId): void
    {
        $this->serviceSystemImportId = $serviceSystemImportId;
    }

    /**
     * @return string|null
     */
    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    /**
     * @param string|null $serviceKey
     */
    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }
}
