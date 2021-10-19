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

namespace App\Admin\Onboarding;


use App\Admin\StateGroup\CommuneAdmin;
use App\Form\Type\CommuneAdministrativeContactType;
use App\Form\Type\CommuneType;
use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FormSolutionAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/formsolutions';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('general', [
                'label' => 'app.form_solution.groups.general',
            ])
            ->with('app.form_solution.groups.general', ['class' => 'col-md-6']);
        $formMapper
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ]);
        $this->addGroupEmailFormField($formMapper);
        $formMapper->add('imageFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            //'delete_label' => '...',
            //'download_label' => '...',
            'download_uri' => true,
            'image_uri' => true,
            'imagine_pattern' => 'default_small',
            'asset_helper' => true,
        ]);
        $formMapper
            ->end()
            ->with('app.form_solution.groups.content_data', ['class' => 'col-md-6']);
        $formMapper
            ->add('privacyText', TextareaType::class, [
                'required' => false,
            ])
            ->add('privacyUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('imprintText', TextareaType::class, [
                'required' => false,
            ])
            ->add('imprintUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('accessibility', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->with('app.form_solution.groups.administration_contact', ['class' => 'col-md-6']);
        $formMapper
            ->add('administrationPhoneNumber', TextType::class, [
                'label' => 'app.commune.entity.administration_phone_number',
                'required' => false,
            ])
            ->add('administrationFaxNumber', TextType::class, [
                'label' => 'app.commune.entity.administration_fax_number',
                'required' => false,
            ])
            ->add('administrationEmail', EmailType::class, [
                'label' => 'app.commune.entity.administration_email',
                'required' => false,
            ])
            ->add('administrationUrl', UrlType::class, [
                'label' => 'app.commune.entity.administration_url',
                'required' => false,
            ]);;
        $formMapper
            ->end()
            ->with('app.form_solution.groups.header_data', ['class' => 'col-md-6']);
        $formMapper
            ->add('letterheadAddress', TextareaType::class, [
                'label' => false,
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->with('app.form_solution.groups.footer_data', ['class' => 'col-md-6']);
        $formMapper
            ->add('openingHours', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->end();
        $formMapper
            ->tab('contacts', [
                'label' => 'app.form_solution.groups.contact_data',
            ])
            ->with('contact_data', [
                'label' => false,
                'class' => 'col-md-12 box-collection-static two-col box-clipboard-rows js-copy-row-values',
            ]);
        $formMapper
            ->add('contacts', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => 'app.form_solution.entity.contacts',
                'entry_type' => OnboardingContactType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
            ]);
        $formMapper
            ->end()
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communeName')
            ->add('officialCommuneKey')
            ->add('imageFile')
            ->add('modifiedAt')
            ->add('privacyText')
            ->add('privacyUrl')
            ->add('imprintText')
            ->add('imprintUrl')
            ->add('accessibility')
            ->add('openingHours')
            ->add('contacts')
            ->add('groupEmail');
    }
}
