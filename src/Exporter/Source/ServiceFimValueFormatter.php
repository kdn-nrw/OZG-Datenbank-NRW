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

namespace App\Exporter\Source;

use App\Entity\Service;

class ServiceFimValueFormatter implements CustomPropertyValueFormatterInterface
{
    public const FIM_DATA_TYPE_PREFIX = 'fimDataType';
    public const FIM_STATUS_PREFIX = 'fimStatus';

    /**
     * @var string[]|array
     */
    protected $fimStatusTranslations = [];

    /**
     * @param array|string[] $fimStatusTranslations
     */
    public function setFimStatusTranslations($fimStatusTranslations): void
    {
        $this->fimStatusTranslations = $fimStatusTranslations;
    }

    /**
     * Returns the property value for the given object or array
     *
     * @param string $propertyPath
     * @param object|array $objectOrArray
     * @return string|null
     */
    public function getPropertyValue(string $propertyPath, $objectOrArray): ?string
    {
        /** @var Service $objectOrArray */
        $value = '';
        if (strpos($propertyPath, self::FIM_DATA_TYPE_PREFIX) === 0) {
            $dataType = str_replace(self::FIM_DATA_TYPE_PREFIX, '', $propertyPath);
            $fimEntry = $objectOrArray->getFimType($dataType);
            if (null !== $fimEntry && $notes = $fimEntry->getNotes()) {
                $value = trim(strip_tags($notes));
            }
        } elseif (strpos($propertyPath, self::FIM_STATUS_PREFIX) === 0) {
            $dataType = str_replace(self::FIM_STATUS_PREFIX, '', $propertyPath);
            $fimEntry = $objectOrArray->getFimType($dataType);
            if (null !== $fimEntry) {
                $value = $this->fimStatusTranslations[$fimEntry->getStatus()];
            }
        }
        return $value;
    }
}