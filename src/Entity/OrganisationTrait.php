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

// use App\Entity\Contact statement is required for API platform!
use App\Entity\Contact;
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
     * @return \App\Entity\Contact[]|Collection
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
