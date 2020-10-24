<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;


use App\Entity\ImplementationStatus;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImplementationStatusAdmin extends StatusAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('level', IntegerType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('setAutomatically', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ])
            ->add('prevStatus', ModelType::class, [
                'btn_add' => false,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('nextStatus', ModelType::class, [
                'btn_add' => false,
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('statusSwitch', ChoiceType::class, [
                'choices' => array_flip(ImplementationStatus::$statusSwitchChoices),
                'required' => false,
            ])
            ->end();
    }
}
