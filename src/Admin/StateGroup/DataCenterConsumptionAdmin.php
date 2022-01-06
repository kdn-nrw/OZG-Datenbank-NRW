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
use App\Admin\Traits\DatePickerTrait;
use App\Entity\StateGroup\DataCenterConsumption;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class DataCenterConsumptionAdmin extends AbstractAppAdmin
{
    use DatePickerTrait;

    private function getYearChoices(): array
    {
        $years = [];
        for ($year = 2010; $year < date('Y') + 1; $year++) {
            $years[$year] = $year;
        }
        return $years;
    }

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('dataCenter')) {
            $form
                ->add('dataCenter', ModelType::class, [
                    'label' => 'app.data_center_consumption.entity.data_center',
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ], [
                    'admin_code' => DataCenterAdmin::class
                ]);
        }

        $form
            ->add('year', ChoiceType::class, [
                'label' => 'app.data_center_consumption.entity.year',
                'choices' => $this->getYearChoices(),
                'required' => true,
                'expanded' => false,
            ])
            ->add('powerConsumption', TextType::class, [
                'label' => 'app.data_center_consumption.entity.power_consumption',
                'required' => true,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'app.data_center_consumption.entity.comment',
                'required' => false,
                //'format' => 'richhtml',
                //'ckeditor_context' => 'default', // optional
            ]);

        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'dataCenter');
        $filter->add('year',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => $this->getYearChoices(),
            ]
        );
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('dataCenter', null, [
                'admin_code' => ServiceProviderAdmin::class
            ])
            ->add('year')
            ->add('powerConsumption')
            ->add('createdBy', null, [
                'template' => 'General/List/list_user.html.twig',
            ]);
        $list->add('_action', null, [
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
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('dataCenter')
            ->add('createdBy', null, [
                'template' => 'General/Show/show-user.html.twig',
            ]);
        $this->addDatePickersShowFields($show, 'createdAt', false);
        $show
            ->add('year')
            ->add('powerConsumption')
            ->add('comment');
    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        if ($object instanceof DataCenterConsumption) {
            $object->setYear(date('Y') - 1);
        }

        return $object;
    }
}
