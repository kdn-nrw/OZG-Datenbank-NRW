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

namespace App\Model;

class ExportSettings
{
    /**
     * Additional export fields
     * @var array|string[]
     */
    private $additionFields = [];

    /**
     * Exclude export fields
     * @var array|string[]
     */
    private $excludeFields = ['hidden', 'slug', 'importId', 'importSource'];

    /**
     * Export formats
     *
     * @var array|string[]
     */
    private $formats = ['xlsx'];

    /**
     * Start export with these fields
     *
     * @var array|string[]
     */
    private $fieldsStart = ['id', 'createdAt', 'modifiedAt', 'createdBy'];

    /**
     * @return array|string[]
     */
    public function getAdditionFields(): array
    {
        return $this->additionFields;
    }

    /**
     * @param array|string[] $additionFields
     */
    public function setAdditionFields(array $additionFields): void
    {
        $this->additionFields = $additionFields;
    }

    /**
     * @return array|string[]
     */
    public function getExcludeFields(): array
    {
        return $this->excludeFields;
    }

    /**
     * @param array|string[] $excludeFields
     */
    public function setExcludeFields(array $excludeFields): void
    {
        $this->excludeFields = $excludeFields;
    }

    /**
     * @param array|string[] $excludeFields
     */
    public function addExcludeFields(array $excludeFields): void
    {
        foreach ($excludeFields as $field) {
            if (!in_array($field, $this->excludeFields, false)) {
                $this->excludeFields[] = $field;
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @param array|string[] $formats
     */
    public function setFormats(array $formats): void
    {
        $this->formats = $formats;
    }

    /**
     * @return array|string[]
     */
    public function getFieldsStart()
    {
        return $this->fieldsStart;
    }

    /**
     * @param array|string[] $fieldsStart
     */
    public function setFieldsStart($fieldsStart): void
    {
        $this->fieldsStart = $fieldsStart;
    }

}
