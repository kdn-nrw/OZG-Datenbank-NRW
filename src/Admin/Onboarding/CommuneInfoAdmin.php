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
use App\Form\Type\CommuneType;
use App\Form\Type\OnboardingContactType;
use App\Form\Type\OnboardingInfoServiceType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CommuneInfoAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/basis';

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->tab('general', [
                'label' => 'app.commune_info.groups.general',
            ])
            ->with('app.commune_info.groups.general', ['class' => 'col-md-6']);
        $this->addDataCompletenessConfirmedField($form);
        $form
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ]);
        $this->addGroupEmailFormField($form);
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
            ->with('app.commune_info.groups.content_data', ['class' => 'col-md-6']);
        $form
            ->add('privacyText', TextareaType::class, [
                'required' => false,
            ])
            ->add('imprintText', TextareaType::class, [
                'required' => false,
            ])
            ->add('accessibility', TextareaType::class, [
                'required' => false,
            ])
            ->end();
        $form
            ->with('group_technical_data', [
                'label' => 'app.commune_info.groups.technical_data',
                'class' => 'clear-left-md  col-md-6',
            ])
            ->add('ipAddress', TextType::class, [
                'required' => false,
                'help' => '<a href="https://www.wieistmeineip.de/" target="_blank">Aktuelle IP-Adresse?</a>',
            ])
            ->end();
        $form
            ->with('app.commune_info.groups.footer_data', ['class' => 'col-md-6']);
        $form
            ->add('openingHours', TextareaType::class, [
                'required' => false,
            ]);
        $form
            ->end()
            ->end();
        $this->addContactFormFields($form);

        $form
            ->with('Services', [
                'label' => 'app.epayment.tabs.services',
                'tab' => true,
            ])
            ->with('epayment_services', [
                'label' => false,
                //'class' => 'col-md-12',
                'class' => 'col-md-12 box-collection-table two-col box-collection-commune-solutions',
            ])
            ->add('communeSolutions', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => 'app.commune_info.entity.commune_solutions',
                'entry_type' => OnboardingInfoServiceType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
                'row_attr' => [
                    'class' => 'display-rows-as-table',
                ],
            ])
            // app.onboarding_commune_solution.object_name
            ->end()
            ->end();
    }

    protected function addContactFormFields(FormMapper $form)
    {
        $form
            ->tab('contacts', [
                'label' => 'app.commune_info.groups.contact_data',
            ])
            ->with('contact_data', [
                'label' => false,
                'class' => 'col-md-12 box-collection-static two-col box-clipboard-rows js-copy-row-values',
            ]);
        $form
            ->add('contacts', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => 'app.commune_info.entity.contacts',
                'entry_type' => OnboardingContactType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
            ]);
        $form
            ->end()
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('communeName')
            ->add('officialCommuneKey')
            ->add('imageFile')
            ->add('modifiedAt')
            ->add('privacyText')
            ->add('imprintText')
            ->add('accessibility')
            ->add('openingHours')
            ->add('contacts')
            ->add('groupEmail')
            ->add('ipAddress');
    }
}
