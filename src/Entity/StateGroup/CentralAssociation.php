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

use App\Entity\AddressTrait;
use App\Entity\Base\BaseNamedEntity;
use App\Entity\Organisation;
use App\Entity\OrganisationEntityInterface;
use App\Entity\OrganisationTrait;
use App\Entity\UrlTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Spitzenverbände
 *
 * @ORM\Entity
 * @ORM\Table(name="central_association")
 * @ORM\HasLifecycleCallbacks
 */
class CentralAssociation extends BaseNamedEntity implements OrganisationEntityInterface
{
    use AddressTrait;
    use UrlTrait;
    use OrganisationTrait;

    /**
     * @var Organisation
     * @ORM\OneToOne(targetEntity="App\Entity\Organisation", inversedBy="centralAssociation", cascade={"all"})
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
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune", mappedBy="centralAssociations")
     */
    private $communes;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
    }

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void
    {
        $this->organisation = $organisation;
        $this->organisation->setCentralAssociation($this);
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
     * @param Commune $commune
     * @return self
     */
    public function addCommune($commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
            $commune->addCentralAssociation($this);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function removeCommune($commune): self
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
            $commune->removeCentralAssociation($this);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
    }

}
