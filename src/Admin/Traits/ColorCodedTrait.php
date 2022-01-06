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

use App\Entity\Base\ColorCodedEntityInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

/**
 * Trait ColorCodedTrait
 */
trait ColorCodedTrait
{
    protected function addColorFormFields(FormMapper $form): void
    {
        $colorChoices = [];
        foreach (ColorCodedEntityInterface::CSS_CLASS_CHOICES as $cssCassName) {
            $colorChoices['app.status.entity.css_class_choices.' . str_replace('-', '_', $cssCassName)] = $cssCassName;
        }
        $form
            ->add('color', ColorType::class, [
                'label' => 'app.status.entity.color',
                'required' => false,
            ])
            ->add('cssClass', ChoiceType::class, [
                'label' => 'app.status.entity.css_class',
                'choices' => $colorChoices,
                'required' => false,
            ]);
    }
}