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

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('app.organisation.groups.basic_data', ['class' => 'col-md-6'])
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
            ->end()
            ->with('app.organisation.groups.address_data', ['class' => 'col-md-6']);
        $this->addAddressFormFields($form);
        $form->end();
        if (!$this->isExcludedFormField('organizationType')) {
            $mapFieldMasks = [
                Organisation::TYPE_DEFAULT => [],
            ];
            foreach (Organisation::$mapFields as $key => $field) {
                $mapFieldMasks[$key] = [$field];
            }
            $form
                ->with('app.organisation.groups.type_data', ['class' => 'col-md-6'])
                ->add('organizationType', ChoiceFieldMaskType::class, [
                    'choices' => Organisation::$organizationTypeChoices,
                    'map' => $mapFieldMasks,
                    'required' => true,
                ]);
            foreach (Organisation::$mapFields as $field) {
                $propertyConfiguration = $this->adminManager->getConfigurationForEntityProperty($this->getClass(), $field);
                $fieldDescriptionOptions = [];
                if (!empty($propertyConfiguration['admin_class'])) {
                    $fieldDescriptionOptions['admin_code'] = $propertyConfiguration['admin_class'];
                }
                $this->addDefaultModelType($form, $field, $fieldDescriptionOptions);
            }
            $form->end();
        }
        $form
            ->end();

        if (!$this->isExcludedFormField('contacts')) {
            $form
                ->with('app.organisation.groups.contacts', ['class' => 'col-md-6']);

            $this->addContactsFormFields($form, true, 'contacts', false, false);
            $form
                ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
        $this->addAddressDatagridFilters($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name');
        $this->addAddressListFields($list);
        $list
            ->add('url', 'url')
            ->add('organizationType', 'choice', [
                'editable' => false,
                'choices' => array_flip(Organisation::$organizationTypeChoices),
                'catalogue' => 'messages',
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $this->addAddressShowFields($show);
        $show->add('url', 'url');
        $this->addContactsShowFields($show);
    }
}
