<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Frontend;


use App\Entity\Onboarding\AbstractOnboardingEntity;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

abstract class AbstractOnboardingAdmin extends AbstractFrontendAdmin
{
    /**
     * List of disabled routes
     *
     * @var string[]
     */
    protected $disabledRoutes = ['edit'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('commune', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ], [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $form
            ->add('status', ChoiceType::class, [
                'label' => 'app.commune_info.entity.status',
                'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                'required' => true,
                'expanded' => true,
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['class' => 'onboarding-status ob-status-' . $value];
                },
            ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $this->addDefaultDatagridFilter($filter, 'commune');
        $filter
            ->add('status', ChoiceFilter::class, [
                'label' => 'app.commune_info.entity.status',
                'field_options' => [
                    'choices' => array_flip(AbstractOnboardingEntity::$statusChoices),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('commune', null, [
                'admin_code' => CommuneAdmin::class,
            ])
            ->add('modifiedAt')
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.commune_info.entity.status',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('commune', null, [
            'admin_code' => CommuneAdmin::class,
        ])
            ->add('modifiedAt')
            ->add('description')
            ->add('status', FieldDescriptionInterface::TYPE_CHOICE, [
                'label' => 'app.commune_info.entity.status',
                'editable' => false,
                'choices' => AbstractOnboardingEntity::$statusChoices,
                'catalogue' => 'messages',
            ]);
    }
}
