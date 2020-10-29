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
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImplementationStatusAdmin extends StatusAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->addDefaultStatusFormFields($formMapper);
        $formMapper
            ->add('setAutomatically', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ])
            ->add('statusSwitch', ChoiceType::class, [
                'choices' => array_flip(ImplementationStatus::$statusSwitchChoices),
                'required' => false,
            ])
            ->end();
    }
}
