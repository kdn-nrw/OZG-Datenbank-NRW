<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Address trait
 */
trait AddressTrait
{
    /**
     * Street
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * Zip code
     * @var string|null
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zipCode;

    /**
     * Town
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $town;

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
    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
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
    public function setTown(?string $town): void
    {
        $this->town = $town;
    }
}
