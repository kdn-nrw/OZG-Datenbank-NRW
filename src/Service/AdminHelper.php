<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Model\Annotation\BaseModelAnnotation;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeRangeFilter;
use Sonata\Form\Type\DateRangePickerType;
use Sonata\Form\Type\DateTimeRangePickerType;

final class AdminHelper
{
    use InjectAdminManagerTrait;

    /**
     * Adds the data grid filter base on the property configuration
     * @param string $modelClass
     * @param DatagridMapper $filter
     * @param string $property
     * @param array $filterOptions
     */
    public function addDefaultDataGridFilter(string $modelClass, DatagridMapper $filter, string $property, array $filterOptions = []): void
    {
        $propertyConfiguration = $this->adminManager->getConfigurationForEntityProperty($modelClass, $property);
        $type = null;
        $fieldDescriptionOptions = [];
        if (!empty($propertyConfiguration['default_label']) && empty($filterOptions['label'])) {
            $filterOptions['label'] = $propertyConfiguration['default_label'];
        }
        $dataType = $propertyConfiguration['data_type'];
        if ($dataType === BaseModelAnnotation::DATA_TYPE_DATE_TIME) {
            $type = DateTimeRangeFilter::class;
            $filterOptions['field_type'] = DateTimeRangePickerType::class;
        } elseif ($dataType === BaseModelAnnotation::DATA_TYPE_DATE) {
            $type = DateRangeFilter::class;
            $filterOptions['field_type'] = DateRangePickerType::class;
        } elseif (!empty($propertyConfiguration['entity_class'])) {
            if (!empty($propertyConfiguration['admin_class'])) {
                $filterOptions['admin_code'] = $propertyConfiguration['admin_class'];
            }
            $fieldDescriptionOptions = ['expanded' => false, 'multiple' => true];
        }
        $filter->add($property, $type, $filterOptions, $fieldDescriptionOptions);
    }

}