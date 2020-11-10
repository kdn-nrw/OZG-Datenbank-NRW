<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api\Annotation;

use App\Import\Annotation\ImportModelAnnotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class ApiSearchModelAnnotation
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 * @Required
 */
class ApiSearchModelAnnotation extends ImportModelAnnotation
{
    /**
     * Toggle custom status for property; Determines if property is added to the default form fields
     * @var bool
     */
    public $customProperty = false;

    /**
     * Toggle searchable status for property; Determines if property is added to the query string
     * @var bool
     */
    public $searchProperty = true;

    /**
     * @return bool
     */
    public function isCustomProperty(): bool
    {
        return $this->customProperty;
    }

    /**
     * @param bool $customProperty
     */
    public function setCustomProperty(bool $customProperty): void
    {
        $this->customProperty = $customProperty;
    }

    /**
     * @return bool
     */
    public function isSearchProperty(): bool
    {
        return $this->searchProperty;
    }

    /**
     * @param bool $searchProperty
     */
    public function setSearchProperty(bool $searchProperty): void
    {
        $this->searchProperty = $searchProperty;
    }

}