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
use App\Admin\Traits\CategoryTrait;
use App\Entity\Contact;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ContactAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use CategoryTrait;
    use AddressTrait;

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('app.contact.groups.person_data', ['class' => 'col-md-6'])
            ->add('gender', ChoiceType::class, [
                'choices' => array_flip(Contact::$genderTypeChoices),
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'required' => false,
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class);
        $form->add('imageFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            //'delete_label' => '...',
            //'download_label' => '...',
            'download_uri' => true,
            'image_uri' => true,
            'imagine_pattern' => 'default_small',
            'asset_helper' => true,
        ]);
        $form
            ->end()
            ->with('app.contact.groups.address_data', ['class' => 'col-md-6']);
        $this->addAddressFormFields($form);
        $form
            ->add('url', UrlType::class, [
                'required' => false
            ]);
        $form->end();
        $form
            ->end()
            ->with('app.contact.groups.organisation', ['class' => 'clear-left-md col-md-6']);
        if (!$this->isExcludedFormField('organisationEntity')) {
            $form->add('organisationEntity', ModelType::class, [
                'label' => 'app.contact.entity.organisation_entity_form',
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ]);
        }
        if (!$this->isExcludedFormField('organisationEntity')) {
            $form->add('organisation', TextType::class, [
                'label' => 'app.contact.entity.organisation_form',
                'required' => false,
            ]);
        }
        $form
            ->add('department', TextType::class, [
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'required' => false,
            ]);
        $this->addCategoriesFormFields($form);
        $form->end();
        $form->with('app.contact.groups.contact', ['class' => 'col-md-6'])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
            ])
            ->add('faxNumber', TextType::class, [
                'required' => false,
            ])
            ->add('mobileNumber', TextType::class, [
                'required' => false,
            ]);
        if (!$this->isExcludedFormField('contactType')) {
            $form->add('contactType', ChoiceType::class, [
                'choices' => [
                    'app.contact.entity.contact_type_choices.default' => Contact::CONTACT_TYPE_DEFAULT,
                    'app.contact.entity.contact_type_choices.cms_address' => Contact::CONTACT_TYPE_IMPORT_CMS,
                ],
                'required' => true,
            ]);
        }
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('firstName');
        $filter->add('lastName');
        $filter->add('email');
        $filter->add('organisation');
        $this->addDefaultDatagridFilter($filter, 'categories');
        $this->addAddressDatagridFilters($filter);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('gender', 'choice', [
                'editable' => false,
                'choices' => Contact::$genderTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email')
            ->add('organisation');
        $this->addAddressListFields($list);

        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('gender', 'choice', [
                'editable' => false,
                'choices' => Contact::$genderTypeChoices,
                'catalogue' => 'messages',
            ])
            ->add('title')
            ->add('lastName')
            ->add('firstName')
            ->add('email');
        $this->addAddressShowFields($show);
        $show->add('url', 'url');
        $show->add('organisation')
            ->add('department')
            ->add('position')
            ->add('phoneNumber')
            ->add('faxNumber')
            ->add('mobileNumber');
        $this->addCategoriesShowFields($show);
    }
}
