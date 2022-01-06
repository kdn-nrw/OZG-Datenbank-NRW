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
use App\Admin\Traits\DatePickerTrait;
use App\Entity\Onboarding\Release;
use App\Form\Type\CommuneType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;

class ReleaseAdmin extends AbstractOnboardingAdmin
{
    use DatePickerTrait;

    protected $baseRoutePattern = 'onboarding/go-live';

    protected function configureFormGroups(FormMapper $form)
    {
        $form
            ->with('general', [
                'label' => 'app.release.groups.general',
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('release', [
                'label' => 'app.release.groups.release',
                'description' => 'app.release.groups.release_description',
                'class' => 'col-md-12',
            ])
            ->end()
            ->with('confirmation', [
                'label' => 'app.release.groups.confirmation',
                'description' => 'app.release.groups.confirmation_description',
                'class' => 'col-md-12',
            ])
            ->end();
    }

    protected function configureFormFields(FormMapper $form)
    {
        $this->configureFormGroups($form);
        $form
            ->with('general');
        $form
            ->add('commune', CommuneType::class, [
                'label' => false,
                //'required' => true,
                'disabled' => true,
                'required' => false
            ], [
                'admin_code' => CommuneAdmin::class,
            ])/*
            ->add('street', TextType::class, [
                'label' => 'app.epayment.entity.street',
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'app.epayment.entity.zip_code',
                'required' => false,
            ])
            ->add('town', TextType::class, [
                'label' => 'app.epayment.entity.town',
                'required' => false,
            ])*/
        ;
        $this->addDataCompletenessConfirmedField($form);

        $form->end();
        $form
            ->with('release');
        $minDate = date_create('+1 week');
        $subject = $this->getSubject();
        if ($subject instanceof Release && (null !== $releaseDate = $subject->getReleaseDate())
            && $releaseDate < $minDate) {
            $minDate = clone $releaseDate;
        }
        $this->addDatePickerFormField($form, 'releaseDate', 3, [
            'dp_min_date' => $minDate,
            'required' => true,
        ]);
        $form->end();
        $form
            ->with('confirmation')
            ->add('releaseConfirmed', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        parent::configureDatagridFilters($filter);
        $this->addDefaultDatagridFilter($filter, 'releaseDate');
    }

    /**
     * Adds the list status field
     *
     * @param ListMapper $list
     */
    protected function addListStatusField(ListMapper $list): void
    {
        $list
            ->add('releaseStatus', 'choice', [
                'label' => 'app.release.entity.release_status',
                'template' => 'Onboarding/Release/list-release-status.html.twig',
                'editable' => false,
                'choices' => array_flip(Release::$releaseStatusChoices),
                //'catalogue' => 'SonataAdminBundle',
            ]);
        $this->addDatePickersListFields($list, 'releaseDate', false, false);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        parent::configureShowFields($show);
        $show
            ->add('releaseStatus', 'choice', [
                'label' => 'app.release.entity.release_status',
                'template' => 'Onboarding/list-status.html.twig',
                'editable' => false,
                'choices' => array_flip(Release::$releaseStatusChoices),
            ]);
        $this->addDatePickersShowFields($show, 'releaseDate', false);
    }
}
