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

use Doctrine\ORM\Mapping as ORM;

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
    protected $importId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="import_source", type="string", length=100, nullable=true)
     */
    protected $importSource;

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
