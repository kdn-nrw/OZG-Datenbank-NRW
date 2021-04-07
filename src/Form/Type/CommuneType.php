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

namespace App\Form\Type;

use App\Entity\StateGroup\Commune;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommuneType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = (bool)$options['disabled'];
        $required = !$disabled;
        $builder->add('name', TextType::class, [
            'label' => 'app.epayment.entity.commune',
            'required' => $required,
            'disabled' => $disabled,
        ]);
        $builder->add('officialCommunityKey', TextType::class, [
            'label' => 'app.commune.entity.official_community_key_long',
            'required' => $required,
            'disabled' => $disabled,
        ]);
        if ($options['show_address']) {
            $builder->add('street', TextType::class, [
                'label' => 'app.commune.entity.street',
                'required' => false,
                'disabled' => $disabled,
            ]);
            $builder->add('zipCode', TextType::class, [
                'label' => 'app.commune.entity.zip_code',
                'required' => false,
                'disabled' => $disabled,
            ]);
            $builder->add('town', TextType::class, [
                'label' => 'app.commune.entity.town',
                'required' => false,
                'disabled' => $disabled,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commune::class,
            'disabled' => true,
            'show_address' => false,
        ]);
    }


}