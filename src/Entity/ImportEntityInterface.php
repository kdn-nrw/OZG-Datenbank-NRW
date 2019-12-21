<?php

namespace App\Entity;


/**
 * Import entity interface
 */
interface ImportEntityInterface
{
    /**
     * @return int|null
     */
    public function getImportId(): ?int;

    /**
     * @param int|null $importId
     */
    public function setImportId(?int $importId): void;

    /**
     * @return string|null
     */
    public function getImportSource(): ?string;

    /**
     * @param string|null $importSource
     */
    public function setImportSource(?string $importSource): void;

}
