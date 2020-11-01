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
use Symfony\Component\Validator\Constraints as Assert;

class ArsAgsDemand extends AbstractDemand
{
    /**
     * @var string|null
     * @ApiSearchModelAnnotation(parameter="suchbegriff", dataType="string", required=true)
     * @Assert\NotBlank
     */
    protected $searchTerm;

    /**
     * @return string|null
     */
    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    /**
     * @param string|null $searchTerm
     */
    public function setSearchTerm(?string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }
}
