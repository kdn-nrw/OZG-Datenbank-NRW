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

namespace App\Import\Annotation;

use App\Model\Annotation\BaseModelAnnotation;
use Doctrine\Common\Annotations\Annotation;

/**
 * Class ImportModelAnnotation
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 * @Required
 */
class ImportModelAnnotation extends BaseModelAnnotation
{

    /**
     * @var string
     */
    public $parameter;

    /**
     * Map the result to the given model class
     *
     * @var string|null
     */
    public $modelClass;

    /**
     * @var bool
     */
    public $disableImport = false;

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @param string $parameter
     */
    public function setParameter(string $parameter): void
    {
        $this->parameter = $parameter;
    }

    /**
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    /**
     * @param string|null $modelClass
     */
    public function setModelClass(?string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return bool
     */
    public function isDisableImport(): bool
    {
        return $this->disableImport;
    }

    /**
     * @param bool $disableImport
     */
    public function setDisableImport(bool $disableImport): void
    {
        $this->disableImport = $disableImport;
    }

}