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

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Admin\Traits\ModelRegionTrait;
use App\Admin\Traits\OrganisationTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ModelRegionProjectAdmin extends AbstractAppAdmin
{
    use AddressTrait;
    use DatePickerTrait;
    use ModelRegionTrait;
    use OrganisationTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('default', ['label' => 'app.model_region_project.tabs.default']);
        $formMapper->with('general', [
            'label' => 'app.model_region_project.tabs.default',
        ]);
        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addDatePickerFormField($formMapper, 'projectStartAt');
        $this->addDatePickerFormField($formMapper, 'projectEndAt', 20);
        $this->addOrganisationsFormFields($formMapper);
        $formMapper
            ->add('usp', TextareaType::class, [
                'required' => false,
            ])
            ->add('communesBenefits', TextareaType::class, [
                'required' => false,
            ])
            ->add('transferableService', TextareaType::class, [
                'required' => false,
            ])
            ->add('transferableStart', TextareaType::class, [
                'required' => false,
            ]);
        $this->addModelRegionsFormFields($formMapper);
        $formMapper->end();
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('name');
        $this->addOrganisationsDatagridFilters($datagridMapper);
        $this->addDatePickersDatagridFilters($datagridMapper, 'projectStartAt');
        $this->addDatePickersDatagridFilters($datagridMapper, 'projectEndAt');
        $datagridMapper
            ->add('description')
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addModelRegionsDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addDatePickersListFields($listMapper, 'projectStartAt');
        $this->addDatePickersListFields($listMapper, 'projectEndAt');
        $this->addOrganisationsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description');
        $this->addDatePickersShowFields($showMapper, 'projectStartAt');
        $this->addDatePickersShowFields($showMapper, 'projectEndAt');
        $showMapper
            ->add('usp')
            ->add('communesBenefits')
            ->add('transferableService')
            ->add('transferableStart');
        $this->addOrganisationsShowFields($showMapper);
        $this->addModelRegionsShowFields($showMapper);
    }
}
