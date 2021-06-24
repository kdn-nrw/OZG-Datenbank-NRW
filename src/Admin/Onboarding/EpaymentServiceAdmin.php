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

use App\Admin\AbstractAppAdmin;
use App\Admin\SolutionAdmin;
use App\Entity\Onboarding\EpaymentService;
use App\Entity\Solution;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class EpaymentServiceAdmin extends AbstractAppAdmin
{
    protected $baseRoutePattern = 'onboarding/epayment-service';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $enableRequiredFields = true;
        $parentFieldDescription = $this->getParentFieldDescription();
        if (null !== $parentFieldDescription) {
            $parentOptions = $parentFieldDescription->getOptions();
            $enableRequiredFields = empty($parentOptions['ba_disable_required_fields']);
        }
        $formMapper
            ->with('general', [
                'label' => 'app.epayment_service.groups.general',
                'class' => 'col-md-12',
            ]);
        $hideFields = $this->getFormHideFields();
        if (!in_array('epayment', $hideFields, false)) {
            $formMapper
                ->add('epayment', ModelAutocompleteType::class, [
                    'property' => ['communeName',],
                    'required' => $enableRequiredFields,
                ], [
                    'admin_code' => \App\Admin\Onboarding\EpaymentAdmin::class
                ]);
        }
        if (!in_array('solution', $hideFields, false)) {
            $formMapper
                ->add('solution', ModelType::class, [
                        'btn_add' => false,
                        'required' => false,
                        'choice_translation_domain' => false,
                        'placeholder' => '',
                        'query' => $this->getSolutionQueryBuilder(),
                    ], [
                        'admin_code' => \App\Admin\Onboarding\OnboardingServiceAdmin::class,
                    ]
                );
        }
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => $enableRequiredFields,
            ])
            ->add('bookingText', TextareaType::class, [
                'required' => $enableRequiredFields,
            ])
            ->add('valueFirstAccountAssignmentInformation', TextareaType::class, [
                'required' => $enableRequiredFields,
            ])
            ->add('valueSecondAccountAssignmentInformation', TextareaType::class, [
                'required' => $enableRequiredFields,
            ])
            ->end();
        $formMapper
            ->with('optional', [
                'label' => 'app.epayment_service.groups.optional',
                'class' => 'col-md-12',
                'description' => 'app.epayment_service.groups.optional_description',
            ]);
        $formMapper
            ->add('costUnit', TextareaType::class, [
                'required' => false,
            ])
            ->add('payers', TextareaType::class, [
                'required' => false,
            ])
            ->add('productDescription', TextareaType::class, [
                'required' => false,
            ])
            ->add('taxNumber', TextareaType::class, [
                'required' => false,
            ])
            ->end();
    }

    /**
     * Returns the query builder for the status
     *
     * @return QueryBuilder
     */
    private function getSolutionQueryBuilder(): QueryBuilder
    {
        /** @var EntityManager $em */
        $em = $this->modelManager->getEntityManager(Solution::class);

        /** @var EpaymentService $subject */
        $subject = $this->getSubject();
        $selectedEntity = $subject->getSolution();
        $queryBuilder = $em->createQueryBuilder()
            ->select('s')
            ->from(Solution::class, 's');
        if (null !== $selectedEntity) {
            $queryBuilder->where($queryBuilder->expr()->orX(
                's.communeType = :communeType',
                $queryBuilder->expr()->eq('s', $selectedEntity->getId())
            ));
        } else {
            $queryBuilder->where('s.communeType = :communeType');
        }
        $queryBuilder->setParameter('communeType', Solution::COMMUNE_TYPE_ALL);
        //$queryBuilder->orderBy('s.name', 'ASC');
        return $queryBuilder;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addDefaultDatagridFilter($datagridMapper, 'epayment');
        $this->addDefaultDatagridFilter($datagridMapper, 'solution');
        /*$datagridMapper->add('description');*/
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('epayment', null, [
                'admin_code' => \App\Admin\Onboarding\EpaymentAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'communeName'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'epayment'],
                ]
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\Onboarding\OnboardingServiceAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'solution'],
                ]
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('epayment', null, [
                'admin_code' => \App\Admin\Onboarding\EpaymentAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => \App\Admin\Onboarding\OnboardingServiceAdmin::class
            ])
            ->add('description')
            ->add('bookingText')
            ->add('valueFirstAccountAssignmentInformation')
            ->add('valueSecondAccountAssignmentInformation')
            ->add('costUnit')
            ->add('payers')
            ->add('productDescription')
            ->add('taxNumber');
    }
}
