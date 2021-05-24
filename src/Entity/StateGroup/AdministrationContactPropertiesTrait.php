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

namespace App\Entity\StateGroup;

use Doctrine\ORM\Mapping as ORM;


/**
 * Administration contact trait
 */
trait AdministrationContactPropertiesTrait
{

    /**
     * @ORM\Column(type="string", name="administration_email", length=255, nullable=true)
     * @var string|null
     */
    protected $administrationEmail;

    /**
     * @ORM\Column(type="string", name="administration_phone_number", length=100, nullable=true)
     * @var string|null
     */
    protected $administrationPhoneNumber;

    /**
     * @ORM\Column(type="string", name="administration_fax_number", length=100, nullable=true)
     * @var string|null
     */
    protected $administrationFaxNumber;

    /**
     * Url
     * @var string|null
     *
     * @ORM\Column(type="string", name="administration_url", length=2048, nullable=true)
     */
    private $administrationUrl;

    /**
     * @return string|null
     */
    public function getAdministrationEmail(): ?string
    {
        return $this->administrationEmail;
    }

    /**
     * @param string|null $administrationEmail
     */
    public function setAdministrationEmail(?string $administrationEmail): void
    {
        $this->administrationEmail = $administrationEmail;
    }

    /**
     * @return string|null
     */
    public function getAdministrationPhoneNumber(): ?string
    {
        return $this->administrationPhoneNumber;
    }

    /**
     * @param string|null $administrationPhoneNumber
     */
    public function setAdministrationPhoneNumber(?string $administrationPhoneNumber): void
    {
        $this->administrationPhoneNumber = $administrationPhoneNumber;
    }

    /**
     * @return string|null
     */
    public function getAdministrationFaxNumber(): ?string
    {
        return $this->administrationFaxNumber;
    }

    /**
     * @param string|null $administrationFaxNumber
     */
    public function setAdministrationFaxNumber(?string $administrationFaxNumber): void
    {
        $this->administrationFaxNumber = $administrationFaxNumber;
    }

    /**
     * @return string|null
     */
    public function getAdministrationUrl(): ?string
    {
        return $this->administrationUrl;
    }

    /**
     * @param string|null $administrationUrl
     */
    public function setAdministrationUrl(?string $administrationUrl): void
    {
        $this->administrationUrl = $administrationUrl;
    }

}
