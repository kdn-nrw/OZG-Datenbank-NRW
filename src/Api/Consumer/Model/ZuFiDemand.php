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

namespace App\Api\Consumer\Model;

use App\Api\Annotation\ApiSearchModelAnnotation;

/**
 * Class ZuFiDemand
 * @package App\Api\Consumer\Model
 * @App\Api\Consumer\Validator\Constraints\ZuFiDemand()
 */
class ZuFiDemand extends AbstractDemand
{
    /**
     * Die Fremdadapter KiTa (99041004000000) und Finanzämter (99102008000000, 99102015000000, 99102011000000, 99102009000000,
     * 99102016000000) werden ebenfalls in der API berücksichtigt.
     */
    public const CUSTOM_SEARCH_KEYS = [
        '99041004000000' => 'Fremdadapter KiTa (99041004000000)',
        '99102008000000' => 'Finanzämter (99102008000000)',
        '99102015000000' => 'Finanzämter (99102015000000)',
        '99102011000000' => 'Finanzämter (99102011000000)',
        '99102009000000' => 'Finanzämter (99102009000000)',
        '99102016000000' => 'Finanzämter (99102016000000)',
    ];

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="leistungsSchluessel", dataType="string", required=false)
     */
    protected $serviceKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="regionalSchluessel", dataType="string", required=false)
     */
    protected $regionalKey;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="plz", dataType="string", required=false)
     */
    protected $zipCode;

    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="leistungsSchluessel", dataType="string", required=false, customProperty=true)
     */
    protected $customKey;

    /**
     * @return string|null
     */
    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    /**
     * @param string|null $serviceKey
     */
    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }

    /**
     * @return string|null
     */
    public function getCustomKey(): ?string
    {
        return $this->customKey;
    }

    /**
     * @param string|null $customKey
     */
    public function setCustomKey(?string $customKey): void
    {
        $this->customKey = $customKey;
    }

    /**
     * @return string|null
     */
    public function getRegionalKey(): ?string
    {
        return $this->regionalKey;
    }

    /**
     * @param string|null $regionalKey
     */
    public function setRegionalKey(?string $regionalKey): void
    {
        $this->regionalKey = $regionalKey;
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
}
