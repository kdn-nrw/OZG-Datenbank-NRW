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

namespace App\Form\Type;

use App\Entity\Search;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchType
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-25
 */
class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('adminId', ChoiceType::class, [
            'label' => 'app.search.entity.admin_id',
            'required' => true,
            'placeholder' => '',
            'choices' => $options['admin_choices'],
            'attr' => [
                'data-sonata-select2' => 'false',
            ]
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'app.search.entity.description',
            'required' => false,
            'attr' => [
                'placeholder' => 'app.search.entity.description',
            ]
        ]);
        $builder->add('route', TextType::class, [
            'label' => 'app.search.entity.route',
            'required' => true,
            'attr' => [
                'placeholder' => 'app.search.entity.route',
            ]
        ]);
        $builder->add('queryString', TextareaType::class, [
            'label' => 'app.search.entity.query_string',
            'required' => false,
            'attr' => [
                'placeholder' => 'app.search.entity.query_string',
            ]
        ]);
        $builder->add('showForAll', CheckboxType::class, [
            'label' => 'app.search.entity.show_for_all',
            'required' => false,
            'attr' => [
                'align_with_widget' => true,
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'admin_choices' => [],
        ]);
    }
}
