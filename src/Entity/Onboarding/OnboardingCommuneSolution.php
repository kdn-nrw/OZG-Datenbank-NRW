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
use App\Entity\Base\SortableEntityInterface;
use App\Entity\Base\SortableEntityTrait;
use App\Entity\MetaData\HasMetaDateEntityInterface;
use App\Entity\Solution;
use App\Entity\StateGroup\Commune;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class OnboardingCommuneSolution service
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_commune_solution")
 */
class OnboardingCommuneSolution extends BaseEntity implements
    SortableEntityInterface,
    HasMetaDateEntityInterface
{
    use SortableEntityTrait;

    /**
     * @var Solution|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Solution", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     */
    private $solution;

    /**
     * @var Commune
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $commune;

    /**
     * @var CommuneInfo|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Onboarding\CommuneInfo", inversedBy="solutions", cascade={"persist"})
     * @ORM\JoinColumn(name="commune_info_id", referencedColumnName="id")
     */
    private $communeInfo;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled_epayment", type="boolean")
     */
    protected $enabledEpayment = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled_municipal_portal", type="boolean")
     */
    protected $enabledMunicipalPortal = true;

    /**
     * @return Solution|null
     */
    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution(Solution $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return Commune
     */
    public function getCommune(): Commune
    {
        if (null === $this->commune && null !== $this->communeInfo) {
            $this->commune = $this->communeInfo->getCommune();
        }
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
     * @return CommuneInfo|null
     */
    public function getCommuneInfo(): ?CommuneInfo
    {
        return $this->communeInfo;
    }

    /**
     * @param CommuneInfo|null $communeInfo
     */
    public function setCommuneInfo(?CommuneInfo $communeInfo): void
    {
        if (null !== $communeInfo && ($this->commune !== $communeInfo->getCommune())) {
            $this->commune = $communeInfo->getCommune();
        }
        $this->communeInfo = $communeInfo;
    }

    /**
     * @return bool
     */
    public function isEnabledEpayment(): bool
    {
        return $this->enabledEpayment;
    }

    /**
     * @param bool $enabledEpayment
     */
    public function setEnabledEpayment(bool $enabledEpayment): void
    {
        $this->enabledEpayment = $enabledEpayment;
    }

    /**
     * @return bool
     */
    public function isEnabledMunicipalPortal(): bool
    {
        return $this->enabledMunicipalPortal;
    }

    /**
     * @param bool $enabledMunicipalPortal
     */
    public function setEnabledMunicipalPortal(bool $enabledMunicipalPortal): void
    {
        $this->enabledMunicipalPortal = $enabledMunicipalPortal;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = '';
        $commune = $this->getCommune();
        $solution = $this->getSolution();
        if (null !== $commune) {
            $name = $commune . '';
        }
        if (null !== $solution) {
            $name .= ($name ? ': ' : '') . $solution . '';
        }
        if (empty($name)) {
            $name = (string)$this->getId();
        }
        return $name;
    }

}
