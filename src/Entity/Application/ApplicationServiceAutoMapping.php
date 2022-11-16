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

namespace App\Entity\Application;

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Service;
use App\Entity\Solution;
use App\Entity\SpecializedProcedure;
use Doctrine\ORM\Mapping as ORM;


/**
 * Indicator for automatic assignment of services to specialized procedures
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_meta_service_specialized_procedure")
 */
class ApplicationServiceAutoMapping extends BaseBlamableEntity
{
    /**
     * @var Service|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $service;

    /**
     * @var Solution|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Solution", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $solution;

    /**
     * @var SpecializedProcedure|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecializedProcedure", cascade={"persist"})
     * @ORM\JoinColumn(name="specialized_procedure_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $specializedProcedure;

    /**
     * Mapping disabled?
     *
     * @var bool|null
     *
     * @ORM\Column(name="disabled", type="boolean", nullable=true)
     */
    protected $disabled;

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service|null $service
     */
    public function setService(?Service $service): void
    {
        $this->service = $service;
    }

    /**
     * @return Solution|null
     */
    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    /**
     * @param Solution|null $solution
     */
    public function setSolution(?Solution $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return SpecializedProcedure|null
     */
    public function getSpecializedProcedure(): ?SpecializedProcedure
    {
        return $this->specializedProcedure;
    }

    /**
     * @param SpecializedProcedure|null $specializedProcedure
     */
    public function setSpecializedProcedure(?SpecializedProcedure $specializedProcedure): void
    {
        $this->specializedProcedure = $specializedProcedure;
    }

    /**
     * @return bool|null
     */
    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * @param bool|null $disabled
     */
    public function setDisabled(?bool $disabled): void
    {
        $this->disabled = $disabled;
    }

}
