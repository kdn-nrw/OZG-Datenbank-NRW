<?php

namespace App\Entity;


use Doctrine\Common\Collections\Collection;

/**
 * OrganisationTrait trait
 * @property Organisation $organisation
 */
trait OrganisationTrait
{

    /**
     * Returns the organisation
     *
     * @return Organisation
     */
    public function getOrganisation(): Organisation
    {
        if (null === $this->organisation) {
            $this->organisation = new Organisation();
        }
        return $this->organisation;
    }

    /**
     * @return Contact[]|Collection
     * @deprecated
     */
    public function getContacts()
    {
        return $this->getOrganisation()->getContacts();
    }

    /**
     * @param Contact[]|Collection $contacts
     * @deprecated
     */
    public function setContacts($contacts): void
    {
        $this->getOrganisation()->setContacts($contacts);
    }
}
