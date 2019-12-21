<?php

namespace App\Entity;


/**
 * Import trait
 */
trait ImportTrait
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="import_id", type="integer", nullable=true)
     */
    private $importId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="import_source", type="string", length=100, nullable=true)
     */
    private $importSource;

    /**
     * @return int|null
     */
    public function getImportId(): ?int
    {
        return $this->importId;
    }

    /**
     * @param int|null $importId
     */
    public function setImportId(?int $importId): void
    {
        $this->importId = $importId;
    }

    /**
     * @return string|null
     */
    public function getImportSource(): ?string
    {
        return $this->importSource;
    }

    /**
     * @param string|null $importSource
     */
    public function setImportSource(?string $importSource): void
    {
        $this->importSource = $importSource;
    }

}
