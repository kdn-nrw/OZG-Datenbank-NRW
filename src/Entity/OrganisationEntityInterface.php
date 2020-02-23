<?php

namespace App\Entity;


use App\Entity\Base\BaseEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Import entity interface
 */
interface OrganisationEntityInterface extends BaseEntityInterface
{

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return bool
     */
    public function isHidden(): bool;

    /**
     * Returns the organisation
     *
     * @return Organisation
     */
    public function getOrganisation(): Organisation;

    /**
     * @param Organisation $organisation
     */
    public function setOrganisation(Organisation $organisation): void;

    /**
     * @return Contact[]|Collection
     */
    public function getContacts();

}
