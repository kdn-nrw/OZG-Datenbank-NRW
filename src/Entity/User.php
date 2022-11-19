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

use App\Entity\ModelRegion\ModelRegion;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;
use Sonata\UserBundle\Model\UserInterface;

/**
 * User entity class
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_user_user")
 */
class User extends BaseUser
{
    public const GENDER_FEMALE = 'f';
    public const GENDER_MALE = 'm';
    public const GENDER_UNKNOWN = 'u';
    /**
     * @var int $id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var \DateTime|null
     * @ORM\Column(nullable=true, type="datetime", name="date_of_birth")
     */
    protected $dateOfBirth;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="firstname", length="64", nullable=true)
     */
    protected $firstname;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="lastname", length="64", nullable=true)
     */
    protected $lastname;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="website", length="64", nullable=true)
     */
    protected $website;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="biography", length="1000", nullable=true)
     */
    protected $biography;

    /**
     * @var string|null
     * @ORM\Column(name="gender", type="string", length="1", nullable=true)
     */
    protected $gender = self::GENDER_UNKNOWN; // set the default to unknown

    /**
     * @var string|null
     * @ORM\Column(name="locale", type="string", length="8", nullable=true)
     */
    protected $locale;

    /**
     * @var string|null
     * @ORM\Column(name="timezone", type="string", length="64", nullable=true)
     */
    protected $timezone;

    /**
     * @var string|null
     * @ORM\Column(name="phone", type="string", length="64", nullable=true)
     */
    protected $phone;

    /**
     * @var string|null
     * @ORM\Column(name="token", type="string", length="255", nullable=true)
     */
    protected $token;

    /**
     * @var string|null
     * @ORM\Column(name="two_step_code", type="string", length="255", nullable=true)
     */
    protected $twoStepVerificationCode;

    /**
     * @var Organisation|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Organisation", cascade={"persist"})
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $organisation;

    /**
     * @var Commune[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\Commune")
     * @ORM\JoinTable(name="ozg_user_commune_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="commune_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $communes;

    /**
     * @var ModelRegion[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\ModelRegion\ModelRegion")
     * @ORM\JoinTable(name="ozg_user_model_region_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="model_region_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $modelRegions;

    /**
     * @var ServiceProvider[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\StateGroup\ServiceProvider")
     * @ORM\JoinTable(name="ozg_user_service_provider_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="service_provider_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $serviceProviders;

    /**
     * @var GroupInterface[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="mb_user_user_group_mm",
     *     joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    protected $groups;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->communes = new ArrayCollection();
        $this->modelRegions = new ArrayCollection();
        $this->serviceProviders = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime|null $dateOfBirth
     */
    public function setDateOfBirth(?\DateTime $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    /**
     * @return string|null
     */
    public function getBiography(): ?string
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     */
    public function setBiography(string $biography): void
    {
        $this->biography = $biography;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        if (!$this->timezone) {
            $this->timezone = 'Europe/Berlin';
        }
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getTwoStepVerificationCode(): string
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * @param string $twoStepVerificationCode
     */
    public function setTwoStepVerificationCode(string $twoStepVerificationCode): void
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;
    }

    /**
     * Hook on pre-persist operations.
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        parent::prePersist();
        // Update FOS user roles (array is generated in getter function)
        $this->setRoles($this->getRoles());
    }

    /**
     * Hook on pre-update operations.
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        parent::preUpdate();
        // Update FOS user roles (array is generated in getter function)
        $this->setRoles($this->getRoles());
    }

    /**
     * We only use groups for setting the roles. Therefore, the roles array must be cleared
     * before adding the group roles!
     *
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        //$roles = $this->roles;
        $roles = [];

        foreach ($this->getGroups() as $group) {
            /** @var Group $group */
            $this->addGroupRolesRecursive($group, $roles);
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Adds the roles for the given group and all subgroups recursively
     *
     * @param Group $group The user group entity
     * @param array $roles The role list for the current user
     * @param array|int[] $addedGroupIds List of group ids that were already added, to prevent infinite loops
     */
    private function addGroupRolesRecursive(Group $group, array &$roles, array $addedGroupIds = []): void
    {
        if (!in_array($group->getId(), $addedGroupIds, true)) {
            $addedGroupIds[] = $group->getId();
            $groupRoles = $group->getRoles();
            foreach ($groupRoles as $role) {
                if (strpos($role, 'ROLE_MINDBASE') === false) {
                    $roles[] = $role;
                }
            }
            $subGroups = $group->getSubGroups();
            foreach ($subGroups as $subGroup) {
                /** @var Group $subGroup */
                $this->addGroupRolesRecursive($subGroup, $roles, $addedGroupIds);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function addCommune(Commune $commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes->add($commune);
        }

        return $this;
    }

    /**
     * @param Commune $commune
     * @return self
     */
    public function removeCommune(Commune $commune): self
    {
        if ($this->communes->contains($commune)) {
            $this->communes->removeElement($commune);
        }

        return $this;
    }

    /**
     * @return Commune[]|Collection
     */
    public function getCommunes()
    {
        if (null === $this->communes) {
            $this->communes = new ArrayCollection();
        }
        return $this->communes;
    }

    /**
     * @param Commune[]|Collection $communes
     */
    public function setCommunes($communes): void
    {
        $this->communes = $communes;
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function addModelRegion(ModelRegion $modelRegion): self
    {
        if (!$this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->add($modelRegion);
        }

        return $this;
    }

    /**
     * @param ModelRegion $modelRegion
     * @return self
     */
    public function removeModelRegion(ModelRegion $modelRegion): self
    {
        if ($this->modelRegions->contains($modelRegion)) {
            $this->modelRegions->removeElement($modelRegion);
        }

        return $this;
    }

    /**
     * @return ModelRegion[]|Collection
     */
    public function getModelRegions()
    {
        return $this->modelRegions;
    }

    /**
     * @param ModelRegion[]|Collection $modelRegions
     */
    public function setModelRegions($modelRegions): void
    {
        $this->modelRegions = $modelRegions;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function addServiceProvider(ServiceProvider $serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
        }

        return $this;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return self
     */
    public function removeServiceProvider(ServiceProvider $serviceProvider): self
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
        }

        return $this;
    }

    /**
     * @return ServiceProvider[]|Collection
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @param ServiceProvider[]|Collection $serviceProviders
     */
    public function setServiceProviders($serviceProviders): void
    {
        $this->serviceProviders = $serviceProviders;
    }

    /**
     * @return Organisation|null
     */
    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    /**
     * @param Organisation|null $organisation
     */
    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function getFullname()
    {
        return sprintf('%s %s', $this->getFirstname(), $this->getLastname());
    }

    /**
     * Returns the gender list.
     *
     * @return array
     */
    public static function getGenderList()
    {
        return [
            'gender_unknown' => self::GENDER_UNKNOWN,
            'gender_female' => self::GENDER_FEMALE,
            'gender_male' => self::GENDER_MALE,
        ];
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $email = $this->getEmail();
        $fullName = trim($this->getFullname());
        if ($fullName && $email) {
            return $fullName . ' ('.$email.')';
        }
        return $email ?: '-';
    }

}
