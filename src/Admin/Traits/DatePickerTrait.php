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

namespace App\Admin\Traits;

use DateTime;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;

/**
 * Trait DatePickerTrait
 * @package App\Admin\Traits
 */
trait DatePickerTrait
{
    protected function addDatePickerFormField(
        FormMapper $form,
        string $fieldName,
        int $maxYearOffset = 2,
        array $customOptions = []): void
    {
        $now = new DateTime();
        $maxYear = (int)$now->format('Y') + $maxYearOffset;
        $options = [
            //'years' => range(2018, (int)$now->format('Y') + 2),
            'dp_min_date' => date_create('2018-01-01 00:00:00'),
            'dp_max_date' => date_create($maxYear . '-12-31 23:59:59'),
            'dp_use_current' => false,
            'datepicker_use_button' => true,
            'required' => false,
        ];
        if (!empty($customOptions)) {
            $options = array_merge($options, $customOptions);
        }
        $form->add($fieldName, DatePickerType::class, $options);
    }

    protected function addDatePickersListFields(
        ListMapper $list,
        string $fieldName,
        $addProgress = false,
        bool $showOnlyMonth = true,
        array $customFieldDescriptionOptions = []
    ): void
    {
        $fieldDescriptionOptions = [
            // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
            'pattern' => $showOnlyMonth ? 'MMMM yyyy' : 'dd.MM.yyyy',
        ];
        if ($addProgress) {
            $fieldDescriptionOptions['template'] = 'General/List/list_date_progress.html.twig';
        }
        if (!empty($customFieldDescriptionOptions)) {
            $fieldDescriptionOptions = array_merge($fieldDescriptionOptions, $customFieldDescriptionOptions);
        }
        $list->add($fieldName, null, $fieldDescriptionOptions);
    }

    public function addDatePickersShowFields(ShowMapper $show, string $fieldName, bool $showOnlyMonth = true): void
    {
        $show
            ->add($fieldName, null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => $showOnlyMonth ? 'MMMM yyyy' : 'dd.MM.yyyy',
            ]);
    }
}