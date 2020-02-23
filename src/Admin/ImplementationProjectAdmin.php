<?php

namespace App\Admin;

use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\OrganisationTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ImplementationStatus;
use DateTime;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\DatePickerType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ImplementationProjectAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use ContactTrait;
    use LaboratoryTrait;
    use OrganisationTrait;
    use SolutionTrait;
    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $now = new DateTime();
        $maxYear = (int) $now->format('Y') + 2;
        $formMapper->add('name', TextType::class);
        $this->addSolutionsFormFields($formMapper);
        $this->addServiceSystemsFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addLaboratoriesFormFields($formMapper);
        $formMapper
            ->add('status', ModelType::class, [
                'btn_add' => false,
                'required' => true,
                'choice_translation_domain' => false,
            ])
            ->add('projectStartAt', DatePickerType::class, [
                //'years' => range(2018, (int)$now->format('Y') + 2),
                'dp_min_date' => new DateTime('2018-01-01 00:00:00'),
                'dp_max_date' => new DateTime($maxYear .'-12-31 23:59:59'),
                'dp_use_current' => false,
                'datepicker_use_button' => true,
                'required' => false,
            ])
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($formMapper, false, false, 'contacts', false);
        $this->addOrganisationsFormFields($formMapper, 'interestedOrganisations');
        $this->addOrganisationsFormFields($formMapper, 'participationOrganisations');
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addLaboratoriesDatagridFilters($datagridMapper);
        $this->addSolutionsDatagridFilters($datagridMapper);
        $this->addServiceSystemsDatagridFilters($datagridMapper);
        $datagridMapper->add('serviceSystems.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $datagridMapper->add('projectStartAt');
        $this->addContactsDatagridFilters($datagridMapper);
        $this->addOrganisationsDatagridFilters($datagridMapper, 'interestedOrganisations');
        $this->addOrganisationsDatagridFilters($datagridMapper, 'participationOrganisations');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ]);
        $this->addServiceSystemsListFields($listMapper);
        //$this->addSolutionsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description')
            ->add('status', 'choice', [
                //'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ])
            ->add('notes', 'html');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        $this->addContactsShowFields($showMapper);
        $this->addOrganisationsShowFields($showMapper, 'interestedOrganisations');
        $this->addOrganisationsShowFields($showMapper,  'participationOrganisations');
        $this->addServiceSystemsShowFields($showMapper);
    }
}
