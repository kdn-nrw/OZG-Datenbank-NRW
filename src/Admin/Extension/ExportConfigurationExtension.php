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

declare(strict_types=1);

namespace App\Admin\Extension;

use App\Admin\CustomExportAdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Admin extension for export settings
 */
class ExportConfigurationExtension extends AbstractAdminExtension
{
    public function configureExportFields(AdminInterface $admin, array $fields): array
    {
        if ($admin instanceof CustomExportAdminInterface) {
            $exportSettings = $admin->getExportSettings();
            $preFields = $exportSettings->getFieldsStart();
            $defaultFields = $fields;
            $fields = [];
            foreach ($preFields as $field) {
                if (in_array($field, $defaultFields, false)) {
                    $fields[] = $field;
                }
            }
            foreach ($defaultFields as $field) {
                if (!in_array($field, $fields, false)) {
                    $fields[] = $field;
                }
            }
            $show = $admin->getShow();
            if (null !== $show) {
                $showFields = array_keys($show->getElements());
                foreach ($showFields as $field) {
                    if (!in_array($field, $fields, false)) {
                        $fields[] = $field;
                    }
                }
            }
            $additionalFields = $exportSettings->getAdditionFields();
            foreach ($additionalFields as $field) {
                if (!in_array($field, $fields, false)) {
                    $fields[] = $field;
                }
            }
            $excludeFields = $exportSettings->getExcludeFields();
            if (!empty($excludeFields)) {
                $fields = array_diff($fields, $excludeFields);
            }
        }
        return $fields;
    }
}
