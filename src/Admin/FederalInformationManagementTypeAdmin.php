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

namespace App\Admin;

use App\Entity\FederalInformationManagementType as FederalInformationManagementEntity;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class FederalInformationManagementTypeAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isExcludedFormField('service')) {
            $formMapper
                ->add('service', ModelType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => ServiceAdmin::class
                ]);
        }
        $formMapper
            ->add('dataType', ChoiceType::class, [
                'choices' => array_flip(FederalInformationManagementEntity::$mapTypes),
                'attr' => [
                    'class' => 'form-control',
                    'data-sonata-select2' => 'false'
                ],
                'label' => false,
                'required' => true,
                'disabled' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'app.federal_information_management_type.entity.status',
                'choices' => array_flip(FederalInformationManagementEntity::$statusChoices),
                'required' => true,
                'expanded' => true,
                'choice_attr' => static function($choice, $key, $value) {
                    return ['class' => 'fim-status fim-status-' . $value];
                },
            ])/*
            ->add('notes', SimpleFormatterType::class, [
                'label' => 'app.federal_information_management_type.entity.notes',
                'required' => false,
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])*/
            ->add('implementationTeamProposal', SimpleFormatterType::class, [
                'label' => 'app.federal_information_management_type.entity.implementation_team_proposal',
                'required' => false,
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);;
        $formMapper->end();
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('service')
            ->add('dataType')
            ->add('status');
        $listMapper->add('_action', null, [
            'label' => 'app.common.actions',
            'translation_domain' => 'messages',
            'actions' => [
                'show' => [],
                'edit' => [],
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('service')
            ->add('dataType')
            ->add('status')
            //->add('notes')
            ->add('implementationTeamProposal');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}
