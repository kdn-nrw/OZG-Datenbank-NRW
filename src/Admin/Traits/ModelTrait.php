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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;

/**
 * Trait ModelTrait
 * @package App\Admin\Traits
 */
trait ModelTrait
{
    /**
     * Default options for optional model type with single selection
     *
     * @param FormMapper $form
     * @param $fieldName
     * @param array $fieldDescriptionOptions
     */
    protected function addDefaultModelType(FormMapper $form, $fieldName, array $fieldDescriptionOptions = []): void
    {
        $form->add($fieldName, ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'choice_translation_domain' => false,
        ], $fieldDescriptionOptions);
    }
}