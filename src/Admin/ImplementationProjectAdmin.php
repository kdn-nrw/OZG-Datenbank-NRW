<?php

namespace App\Admin;

use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\LaboratoryTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Admin\Traits\SolutionTrait;
use App\Entity\ImplementationStatus;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ImplementationProjectAdmin extends AbstractAppAdmin implements SearchableAdminInterface
{
    use ContactTrait;
    use LaboratoryTrait;
    use SolutionTrait;
    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
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
            ->add('notes', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ]);
        $this->addContactsFormFields($formMapper, false, false, 'contacts', false);
        $this->addContactsFormFields($formMapper, false, false, 'interestedContacts', false);
        $this->addContactsFormFields($formMapper, false, false, 'participationContacts', false);
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
        $this->addContactsDatagridFilters($datagridMapper);
        $this->addContactsDatagridFilters($datagridMapper, 'interestedContacts');
        $this->addContactsDatagridFilters($datagridMapper, 'participationContacts');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addSolutionsListFields($listMapper);
        $this->addServiceSystemsListFields($listMapper);
        $listMapper
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ]);
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
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('notes', 'html');
        $this->addLaboratoriesShowFields($showMapper);
        $this->addSolutionsShowFields($showMapper);
        $this->addContactsShowFields($showMapper);
        $this->addContactsShowFields($showMapper, false, 'interestedContacts');
        $this->addContactsShowFields($showMapper, false, 'participationContacts');
        $this->addServiceSystemsShowFields($showMapper);
    }
}
