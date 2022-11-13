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

namespace App\Admin\StateGroup;

use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\SolutionAdmin;
use App\Admin\Traits\ContactTrait;
use App\Admin\Traits\DatePickerTrait;
use App\Entity\Contact;
use App\Entity\StateGroup\CommuneSolution;
use App\Exporter\Source\ManyEntitiesValueFormatter;
use App\Model\ExportSettings;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class CommuneSolutionAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ContactTrait;
    use DatePickerTrait;

    protected function configureFormFields(FormMapper $form)
    {
        if (!$this->isExcludedFormField('commune')) {
            $form
                ->add('commune', ModelAutocompleteType::class, [
                    'property' => 'name',
                    'required' => true,
                ], [
                    'admin_code' => CommuneAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('solution')) {
            $form
                ->add('solution', ModelAutocompleteType::class, [
                    'property' => ['name', 'description'],
                    'required' => true,
                ], [
                    'admin_code' => SolutionAdmin::class
                ]);
        }
        $form
            ->add('connectionPlanned', ChoiceFieldMaskType::class, [
                'choices' => [
                    'app.commune_solution.entity.connection_planned_choices.no' => false,
                    'app.commune_solution.entity.connection_planned_choices.yes' => true,
                ],
                'map' => [
                    false => [],
                    true => ['connectionPlannedAt'],
                ],
                'required' => false,
            ]);
        $this->addDatePickerFormField($form, 'connectionPlannedAt', 5);

        $form
            ->add('specializedProcedure', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'choice_translation_domain' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ]);
        $form->add('contacts', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
            'query' => $this->getContactOrganisationQueryBuilder()
        ]);
        $form
            ->end();
    }

    /**
     * Returns the query builder for the constituencies (sub-set of communes)
     *
     * @return QueryBuilder
     */
    private function getContactOrganisationQueryBuilder(): ?QueryBuilder
    {
        $subject = $this->getSubject();
        if ($subject instanceof CommuneSolution && $commune = $subject->getCommune()) {
            /** @var EntityManager $em */
            $em = $this->modelManager->getEntityManager(Contact::class);

            $queryBuilder = $em->createQueryBuilder()
                ->select('c')
                ->from(Contact::class, 'c')
                ->where('c.organisationEntity = :organisation')
                ->setParameter('organisation', $commune->getOrganisation())
                ->orderBy('c.lastName', 'ASC');
            return $queryBuilder;
        }
        return null;
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'commune');
        $this->addDefaultDatagridFilter($filter, 'solution');
        /*$filter->add('description');
        $filter->add('status');*/
        $filter
            ->add('connectionPlanned', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => [
                        'app.commune_solution.entity.connection_planned_choices.no' => false,
                        'app.commune_solution.entity.connection_planned_choices.yes' => true,
                    ],
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    //'choice_translation_domain' => 'SonataAdminBundle',
                ],
                'field_type' => ChoiceType::class,
            ]);
        $filter->add('specializedProcedure');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('commune', null, [
                'admin_code' => CommuneAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            ->add('connectionPlanned', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ])
            ->add('specializedProcedure');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('commune', null, [
                'admin_code' => CommuneAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => SolutionAdmin::class
            ])
            ->add('description');
        $show
            ->add('connectionPlanned', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => [
                    false => 'app.commune_solution.entity.connection_planned_choices.no',
                    true => 'app.commune_solution.entity.connection_planned_choices.yes',
                ],
            ]);
        $this->addDatePickersShowFields($show, 'connectionPlannedAt', false);

        $show
            ->add('specializedProcedure')
            ->add('comment', TextareaType::class, [
                'required' => false,
            ]);
        $this->addContactsShowFields($show);
    }

    /**
     * Custom export settings for this admin
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['communeType']);
        $customServiceFormatter = new ManyEntitiesValueFormatter();
        $settings->addCustomPropertyValueFormatter('contacts', $customServiceFormatter);
        return $settings;
    }
}
