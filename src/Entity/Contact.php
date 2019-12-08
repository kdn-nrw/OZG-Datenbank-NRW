<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\HideableEntityTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Contact
 *
 * @ORM\Entity()
 * @ORM\Table(name="ozg_contact")
 * @ORM\HasLifecycleCallbacks
 */
class Contact extends BaseEntity
{
    use HideableEntityTrait;

    /**
     * @ORM\Column(type="string", name="first_name", length=100, nullable=true)
     * @var string|null
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", length=100, nullable=true)
     * @var string|null
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", name="email", length=255, nullable=true)
     * @var string|null
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="zipcode", length=20, nullable=true)
     * @var string|null
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", name="town", length=255, nullable=true)
     * @var string|null
     */
    private $town;

    /**
     * @ORM\Column(type="string", name="street", length=255, nullable=true)
     * @var string|null
     */
    private $street;

    /**
     * @ORM\Column(type="string", name="organisation", length=255, nullable=true)
     * @var string|null
     */
    private $organisation;

    /**
     * @ORM\Column(type="string", name="position", length=255, nullable=true)
     * @var string|null
     */
    private $position;

    /**
     * @ORM\Column(type="string", name="phone_number", length=100, nullable=true)
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     */
    public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string|null
     */
    public function getTown(): ?string
    {
        return $this->town;
    }

    /**
     * @param string|null $town
     */
    public function setTown(?string $town)
    {
        $this->town = $town;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet(?string $street)
    {
        $this->street = $street;
    }


    /**
     * @return string|null
     */
    public function getOrganisation(): ?string
    {
        return $this->organisation;
    }

    /**
     * @param string|null $organisation
     */
    public function setOrganisation(?string $organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     */
    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }


}
