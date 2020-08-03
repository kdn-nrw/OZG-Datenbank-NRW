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

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Modelregion Zuwendungsempfänger
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_beneficiary")
 * @ORM\HasLifecycleCallbacks
 */
class ModelRegionBeneficiary extends BaseNamedEntity implements OrganisationEntityInterface
{
    use AddressTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="Organisation", inversedBy="modelRegionBeneficiary", cascade={"all"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $organisation;

    /**
     * Short name
     * @var string|null
     *
     * @ORM\Column(type="string", name="short_name", length=255, nullable=true)
     */
    private $shortName;

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setModelRegionBeneficiary($this);
    }

    /**
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string|null $shortName
     */
    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return ModelRegionProject[]|Collection
     */
    public function getModelRegionProjects()
    {
        // Notice: Undefined index: targetToSourceKeyColumns
        if ((null !== $organisation = $this->getOrganisation())) {
            $collection = $organisation->getModelRegionProjects();
            if (null !== $collection && $collection->count() > 0) {
                return $collection;
            }
        }
        return new ArrayCollection();
    }
}
