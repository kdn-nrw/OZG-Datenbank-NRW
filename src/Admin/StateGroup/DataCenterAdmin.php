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

namespace App\Admin\StateGroup;


use App\Admin\AbstractAppAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ServiceProviderTrait;
use App\Entity\StateGroup\DataCenter;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DataCenterAdmin extends AbstractAppAdmin
{
    use ServiceProviderTrait;

    protected $baseRoutePattern = 'state/data-center';

    use CommuneTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        if (!$this->isExcludedFormField('serviceProvider')) {
            $form
                ->add('serviceProvider', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => true,
                    'choice_translation_domain' => false,
                ], [
                    'admin_code' => ServiceProviderAdmin::class
                ]);
        }
        $form
            ->add('operationType', ChoiceFieldMaskType::class, [
                'choices' => array_flip(DataCenter::$operationTypeChoices),
                'map' => [
                    DataCenter::OPERATION_TYPE_OWN => ['dataCenterWasteHeat', 'dataCenterWaterCooling', 'consumptions'],
                    DataCenter::OPERATION_TYPE_JOINT => ['jointDataCenterInfo', 'otherServiceProviders',
                        'dataCenterWasteHeat', 'dataCenterWaterCooling', 'consumptions'],
                    DataCenter::OPERATION_TYPE_NONE => [],
                ],
                'required' => true,
            ]);
        $this->addServiceProvidersFormFields($form, 'otherServiceProviders');
        $form
            ->add('jointDataCenterInfo', TextareaType::class, [
                'required' => false,
            ]);
        $form
            ->add('dataCenterWasteHeat', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.data_center.entity.data_center_waste_heat_choices.no' => false,
                    'app.data_center.entity.data_center_waste_heat_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['dataCenterWasteHeatInfo'],
                ],
                'required' => false,
            ])
            ->add('dataCenterWasteHeatInfo', TextareaType::class, [
                'required' => false,
            ]);
        $form
            ->add('dataCenterWaterCooling', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.data_center.entity.data_center_water_cooling_choices.no' => false,
                    'app.data_center.entity.data_center_water_cooling_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['dataCenterWaterCoolingInfo'],
                ],
                'required' => false,
            ])
            ->add('dataCenterWaterCoolingInfo', TextareaType::class, [
                'required' => false,
            ]);

        $form->add('consumptions', CollectionType::class, [
            'type_options' => [
                'delete' => true,
            ],
            'by_reference' => false,
        ], [
            'edit' => 'inline',
            'inline' => 'table',
            'ba_custom_exclude_fields' => ['dataCenter'],
        ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        //$filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'serviceProvider');
        $filter->add('dataCenterWasteHeat')
            ->add('dataCenterWaterCooling');
        $this->addDefaultDatagridFilter($filter, 'otherServiceProviders');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('serviceProvider')
            ->add('operationType', 'choice', [
                'editable' => false,
                'choices' => DataCenter::$operationTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('dataCenterWasteHeat')
            ->add('dataCenterWaterCooling');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('serviceProvider')
            ->add('operationType', 'choice', [
                'editable' => false,
                'choices' => DataCenter::$operationTypeChoices,
                'catalogue' => 'messages',
            ]);
        $this->addServiceProvidersShowFields($show, 'otherServiceProviders');
        $show
            ->add('jointDataCenterInfo')
            ->add('dataCenterWasteHeat')
            ->add('dataCenterWasteHeatInfo')
            ->add('dataCenterWaterCooling')
            ->add('dataCenterWaterCoolingInfo')
            ->add('consumptions');
    }

    /**
     * @inheritDoc
     */
    protected function alterNewInstance(object $object): void
    {
        if ($object instanceof DataCenter) {
            $object->setOperationType(DataCenter::OPERATION_TYPE_NONE);
            $object->setDataCenterWasteHeat(false);
            $object->setDataCenterWaterCooling(false);
        }
    }
}
