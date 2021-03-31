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


use App\Form\Type\OnboardingContactType;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CommuneInfoAdmin extends AbstractOnboardingAdmin
{
    protected $baseRoutePattern = 'onboarding/basis';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('app.commune_info.groups.general', ['class' => 'col-md-6']);
        $formMapper
            ->add('communeName', TextType::class, [
                //'required' => true,
                'disabled' => true,
                'required' => false,
            ])
            ->add('officialCommuneKey', TextType::class, [
                //'required' => true,
                'disabled' => true,
                'required' => false,
            ]);
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
            ->with('app.commune_info.groups.content_data', ['class' => 'col-md-6']);
        $formMapper
            ->add('privacyText', TextType::class, [
                'required' => false,
            ])
            ->add('privacyUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('imprintText', TextType::class, [
                'required' => false,
            ])
            ->add('imprintUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('accessibility', TextType::class, [
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->with('app.commune_info.groups.footer_data', ['class' => 'col-md-6']);
        $formMapper
            ->add('openingHours', TextType::class, [
                'required' => false,
            ]);
        $formMapper
            ->end()
            ->with('app.commune_info.groups.contact_data', [
                'class' => 'col-md-12 box-collection-static two-col',
            ]);
        $formMapper
            ->add('contacts', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, [
                'label' => 'app.commune_info.entity.contacts',
                'entry_type' => OnboardingContactType::class,
                'entry_options' => [
                    'parent_admin' => $this,
                ],
                'row_attr' => [
                    'class' => 'form-group form-group-head',
                ],
            ]);
        $formMapper
            ->end();
    }
}
