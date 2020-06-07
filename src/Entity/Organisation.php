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

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SoftdeletableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Organisation
 * Note: Instead of class table inheritance we use organisation with 1:1 relations because the performance is better!
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_organisation")
 */
class Organisation extends BaseNamedEntity
{
    public const TYPE_DEFAULT = 'default';
    public const TYPE_COMMUNE = 'commune';
    public const TYPE_MANUFACTURER = 'manufacturer';
    public const TYPE_MINISTRY_STATE = 'ministry_state';
    public const TYPE_SERVICE_PROVIDER = 'service_provider';
    public const TYPE_CENTRAL_ASSOCIATION = 'central_association';

    /**
     * @var array Supported positions
     */
    public static $organizationTypeChoices = [
        'app.organisation.entity.organization_type_choices.default' => Organisation::TYPE_DEFAULT,
        'app.organisation.entity.organization_type_choices.commune' => Organisation::TYPE_COMMUNE,
        'app.organisation.entity.organization_type_choices.manufacturer' => Organisation::TYPE_MANUFACTURER,
        'app.organisation.entity.organization_type_choices.ministry_state' => Organisation::TYPE_MINISTRY_STATE,
        'app.organisation.entity.organization_type_choices.service_provider' => Organisation::TYPE_SERVICE_PROVIDER,
        'app.organisation.entity.organization_type_choices.central_association' => Organisation::TYPE_CENTRAL_ASSOCIATION,
    ];

    /**
     * @var array Map constants to fields
     */
    public static $mapFields = [
        Organisation::TYPE_COMMUNE => 'commune',
        Organisation::TYPE_MINISTRY_STATE => 'ministryState',
        Organisation::TYPE_MANUFACTURER => 'manufacturer',
        Organisation::TYPE_SERVICE_PROVIDER => 'serviceProvider',
        Organisation::TYPE_CENTRAL_ASSOCIATION => 'centralAssociation',
    ];

    use AddressTrait;
    use UrlTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $organizationType;

    /**
     * @var Contact[]|Collection
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="organisationEntity", cascade={"all"})
     */
    private $contacts;

    /**
     * @var MinistryState|null
     * @ORM\OneToOne(targetEntity="MinistryState", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="ministry_state_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $ministryState;

    /**
     * @var ServiceProvider|null
     * @ORM\OneToOne(targetEntity="ServiceProvider", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $serviceProvider;

    /**
     * @var Manufacturer|null
     * @ORM\OneToOne(targetEntity="Manufacturer", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $manufacturer;

    /**
     * @var Commune|null
     * @ORM\OneToOne(targetEntity="Commune", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="commune_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $commune;

    /**
     * @var CentralAssociation|null
     * @ORM\OneToOne(targetEntity="CentralAssociation", mappedBy="organisation", cascade={"all"})
     * @ORM\JoinColumn(name="central_association_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $centralAssociation;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getOrganizationType(): ?string
    {
        return $this->organizationType;
    }

    /**
     * @param string|null $organizationType
     */
    public function setOrganizationType(?string $organizationType): void
    {
        $this->organizationType = $organizationType;
    }


    /**
     * @param Contact $contact
     * @return self
     */
    public function addContact($contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setOrganisationEntity($this);
        }

        return $this;
    }

    /**
     * @param Contact $contact
     * @return self
     */
    public function removeContact($contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->setOrganisationEntity(null);
            if ($contact instanceof SoftdeletableEntityInterface) {
                $contact->setDeletedAt(new DateTime());
            }
        }

        return $this;
    }

    /**
     * @return Contact[]|Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param Contact[]|Collection $contacts
     */
    public function setContacts($contacts): void
    {
        $this->contacts = $contacts;
    }

    /**
     * Set property value from reference entity
     * @param OrganisationEntityInterface $refEntity
     */
    public function setFromReference(OrganisationEntityInterface $refEntity): void
    {
        $this->setHidden($refEntity->isHidden());
        $properties = ['name', 'street', 'zipCode', 'town', 'url'];
        foreach ($properties as $property) {
            $getter = 'get' . ucfirst($property);
            $value = $this->$getter();
            $setter = 'set' . ucfirst($property);
            if (method_exists($refEntity, $getter) && $value !== $refEntity->$getter()) {
                $refEntity->$setter($value);
            }
        }
        if ($refEntity instanceof Commune) {
            $this->setOrganizationType(self::TYPE_COMMUNE);
            $this->setCommune($refEntity);
        } elseif ($refEntity instanceof Manufacturer) {
            $this->setOrganizationType(self::TYPE_MANUFACTURER);
            $this->setManufacturer($refEntity);
        } elseif ($refEntity instanceof MinistryState) {
            $this->setOrganizationType(self::TYPE_MINISTRY_STATE);
            $this->setMinistryState($refEntity);
        } elseif ($refEntity instanceof ServiceProvider) {
            $this->setOrganizationType(self::TYPE_SERVICE_PROVIDER);
            $this->setServiceProvider($refEntity);
        } elseif ($refEntity instanceof CentralAssociation) {
            $this->setOrganizationType(self::TYPE_CENTRAL_ASSOCIATION);
            $this->setCentralAssociation($refEntity);
        }
    }

    /**
     * @return MinistryState|null
     */
    public function getMinistryState(): ?MinistryState
    {
        return $this->ministryState;
    }

    /**
     * @param MinistryState|null $ministryState
     */
    public function setMinistryState(?MinistryState $ministryState): void
    {
        $this->ministryState = $ministryState;
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
     * @return Manufacturer|null
     */
    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer|null $manufacturer
     */
    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return Commune|null
     */
    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    /**
     * @param Commune|null $commune
     */
    public function setCommune(?Commune $commune): void
    {
        $this->commune = $commune;
    }

    /**
     * @return CentralAssociation|null
     */
    public function getCentralAssociation(): ?CentralAssociation
    {
        return $this->centralAssociation;
    }

    /**
     * @param CentralAssociation|null $centralAssociation
     */
    public function setCentralAssociation(?CentralAssociation $centralAssociation): void
    {
        $this->centralAssociation = $centralAssociation;
    }

}
