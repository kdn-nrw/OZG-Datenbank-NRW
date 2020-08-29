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

use App\Admin\Traits\AddressTrait;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\ModelTrait;
use App\Entity\Organisation;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class OrganisationAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ContactTrait;
    use AddressTrait;
    use ModelTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $hideFields = $this->getFormHideFields();
        $formMapper
            ->with('app.organisation.groups.basic_data', ['class' => 'col-md-6'])
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
            ->end()
            ->with('app.organisation.groups.address_data', ['class' => 'col-md-6']);
        $this->addAddressFormFields($formMapper);
        $formMapper->end();
        if (!in_array('organizationType', $hideFields, false)) {
            $mapFieldMasks = [
                Organisation::TYPE_DEFAULT => [],
            ];
            foreach (Organisation::$mapFields as $key => $field) {
                $mapFieldMasks[$key] = [$field];
            }
            $formMapper
                ->with('app.organisation.groups.type_data', ['class' => 'col-md-6'])
                ->add('organizationType', ChoiceFieldMaskType::class, [
                    'choices' => Organisation::$organizationTypeChoices,
                    'map' => $mapFieldMasks,
                    'required' => true,
                ]);
            foreach (Organisation::$mapFields as $field) {
                $this->addDefaultModelType($formMapper, $field);
            }
            $formMapper->end();
        }
        $formMapper
            ->end()
            ->with('app.organisation.groups.contacts', ['class' => 'col-md-6']);

        $this->addContactsFormFields($formMapper, false, true, 'contacts', false, false);
        $formMapper
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addAddressDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $this->addAddressListFields($listMapper);
        $listMapper
            ->add('url', 'url')
            ->add('organizationType', 'choice', [
                'editable' => false,
                'choices' => array_flip(Organisation::$organizationTypeChoices),
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name');
        $this->addAddressShowFields($showMapper);
        $showMapper->add('url', 'url');
        $this->addContactsShowFields($showMapper);
    }
}
