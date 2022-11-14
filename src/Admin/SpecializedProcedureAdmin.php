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

use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\ServiceProviderTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SpecializedProcedureAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use CommuneTrait;
    use ManufaturerTrait;
    use ServiceProviderTrait;

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', TextType::class);
        $this->addManufaturersFormFields($form);
        $form
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServiceProvidersFormFields($form);
        $this->addCommunesFormFields($form);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'manufacturers');
        $this->addDefaultDatagridFilter($filter, 'communes');
        $this->addDefaultDatagridFilter($filter, 'serviceProviders');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name');
        $this->addManufaturersListFields($list);
        $list
            ->add('references', 'string', [
                'label' => 'app.specialized_procedure.entity.commune_count',
                'template' => 'SpecializedProcedureAdmin/list-references.html.twig',
                'filterParamName' => 'specializedProcedures',
                'referenceLabel' => 'app.commune.type_label',
                //https://ozg.ddev.site/admin/state/commune/list?filter%5BfullText%5D%5Btype%5D=&filter%5BfullText%5D%5Bvalue%5D=&filter%5Borganisation__contacts%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Bvalue%5D=&filter%5Borganisation__town%5D%5Btype%5D=&filter%5Borganisation__town%5D%5Bvalue%5D=&filter%5BserviceProviders%5D%5Btype%5D=&filter%5BcentralAssociations%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Bvalue%5D=11&filter%5B_page%5D=1&filter%5B_sort_by%5D=name&filter%5B_sort_order%5D=ASC
                //https://ozg.ddev.site/admin/state/commune/list?filter%5BfullText%5D%5Btype%5D=&filter%5BfullText%5D%5Bvalue%5D=&filter%5Borganisation__contacts%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Btype%5D=&filter%5Borganisation__zipCode%5D%5Bvalue%5D=&filter%5Borganisation__town%5D%5Btype%5D=&filter%5Borganisation__town%5D%5Bvalue%5D=&filter%5BserviceProviders%5D%5Btype%5D=&filter%5BcentralAssociations%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Btype%5D=&filter%5BspecializedProcedures%5D%5Bvalue%5D%5B%5D=7&filter%5Bportals%5D%5Btype%5D=&filter%5BpaymentPlatforms%5D%5Btype%5D=&filter%5Blaboratories%5D%5Btype%5D=&filter%5Bconstituency%5D%5Btype%5D=&filter%5BadministrativeDistrict%5D%5Btype%5D=&filter%5BcommuneType%5D%5Btype%5D=&filter%5BofficialCommunityKey%5D%5Btype%5D=&filter%5BofficialCommunityKey%5D%5Bvalue%5D=&filter%5BregionalKey%5D%5Btype%5D=&filter%5BregionalKey%5D%5Bvalue%5D=&filter%5Bname%5D%5Btype%5D=&filter%5Bname%5D%5Bvalue%5D=&filter%5B_page%5D=1&filter%5B_sort_by%5D=sorting&filter%5B_sort_order%5D=ASC&filter%5B_per_page%5D=32
                'route' => [
                    'prefix' => 'admin_app_stategroup_commune',
                    'name' => 'list',
                ],
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('description');
        $this->addManufaturersShowFields($show);
        $this->addServiceProvidersShowFields($show);
        $this->addCommunesShowFields($show);
    }
}
