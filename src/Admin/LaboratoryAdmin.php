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

use App\Admin\Traits\ServiceProviderTrait;
use App\Admin\Traits\ServiceTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class LaboratoryAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ServiceTrait;
    use ServiceProviderTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ]);
        $this->addServiceProvidersFormFields($form);
        $form
            ->add('participantsOther', TextareaType::class, [
                'required' => false,
            ]);
        $this->addServicesFormFields($form);
        $form->add('implementationUrl', UrlType::class, [
                'required' => false,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'serviceProviders');
        $this->addDefaultDatagridFilter($filter, 'services');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('url', 'url');
        $this->addServicesListFields($list);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('description')
            ->add('url', 'url')
            ->add('participantsOther');
        $this->addServiceProvidersShowFields($show);
        $this->addServicesShowFields($show);
    }
}
