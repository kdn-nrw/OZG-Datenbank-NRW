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
use App\Entity\Organisation;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class OrganisationAdmin extends AbstractAppAdmin
{
    use ContactTrait;
    use AddressTrait;

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
            $formMapper
                ->with('app.organisation.groups.type_data', ['class' => 'col-md-6'])
                ->add('organizationType', ChoiceFieldMaskType::class, [
                    'choices' => Organisation::$organizationTypeChoices,
                    'map' => [
                        Organisation::TYPE_DEFAULT => [],
                        Organisation::TYPE_COMMUNE => ['commune'],
                        Organisation::TYPE_MINISTRY_STATE => ['ministryState'],
                        Organisation::TYPE_MANUFACTURER => ['manufacturer'],
                        Organisation::TYPE_SERVICE_PROVIDER => ['serviceProvider'],
                    ],
                    'required' => true,
                ])
                ->add('commune', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('manufacturer', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('ministryState', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('serviceProvider', ModelType::class, [
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_translation_domain' => false,
                ]);
            $formMapper->end();
        }
        $formMapper
            ->end()
            ->with('app.organisation.groups.contacts', ['class' => 'col-md-6']);

        $this->addContactsFormFields($formMapper, false, true, 'contacts', false);
        $formMapper
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
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
