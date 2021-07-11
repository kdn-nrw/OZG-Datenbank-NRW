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

namespace App\Admin\Traits;

/**
 * Trait IsExcludedFormField
 *
 * @method getParentFieldDescription(): ?FieldDescriptionInterface
 * @method hasParentFieldDescription(): bool
 *
 */
trait IsExcludedFormField
{
    /**
     * @var array|string[]
     */
    protected $excludeChildFormFields;

    /**
     * Returns true if the form
     * @param string $fieldName
     * @return bool
     */
    final protected function isExcludedFormField(string $fieldName): bool
    {
        if (null === $this->excludeChildFormFields) {
            $this->excludeChildFormFields = [];
            if ($this->hasParentFieldDescription()
                && null !== $parentFieldDescription = $this->getParentFieldDescription()) {
                $parentOptions = $parentFieldDescription->getOptions();
                if (!empty($parentOptions['ba_custom_exclude_fields'])) {
                    $this->excludeChildFormFields = $parentOptions['ba_custom_exclude_fields'];
                }
            }
        }
        return in_array($fieldName, $this->excludeChildFormFields, false);
    }
}