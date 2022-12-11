<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Onboarding;

use App\Admin\AbstractAppAdmin;
use App\Admin\Base\AuditedEntityAdminInterface;
use App\Admin\Base\AuditedEntityAdminTrait;
use App\Entity\Onboarding\PmPaymentService;
use App\Entity\Solution;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;


class PmPaymentServiceAdmin extends AbstractAppAdmin implements AuditedEntityAdminInterface
{
    use AuditedEntityAdminTrait;
    protected $baseRoutePattern = 'onboarding/pm-payment-service';

    protected function configureFormFields(FormMapper $form)
    {
        $enableRequiredFields = true;
        $parentFieldDescription = $this->getParentFieldDescription();
        if (null !== $parentFieldDescription) {
            $parentOptions = $parentFieldDescription->getOptions();
            $enableRequiredFields = empty($parentOptions['ba_disable_required_fields']);
        }
        $form
            ->with('general', [
                'label' => 'app.pm_payment_service.groups.general',
                'class' => 'col-md-12',
            ]);
        if (!$this->isExcludedFormField('pmPayment')) {
            $form
                ->add('pmPayment', ModelAutocompleteType::class, [
                    'property' => ['communeName',],
                    'required' => $enableRequiredFields,
                ], [
                    'admin_code' => PmPaymentAdmin::class
                ]);
        }
        if (!$this->isExcludedFormField('solution')) {
            $form
                ->add('solution', ModelType::class, [
                        'btn_add' => false,
                        'required' => false,
                        'choice_translation_domain' => false,
                        'placeholder' => '',
                        'query' => $this->getSolutionQueryBuilder(),
                        'disabled' => $this->isExcludedFormField('pmPayment'),
                    ], [
                        'admin_code' => OnboardingServiceAdmin::class,
                    ]
                );
        }
        $form
            ->add('paymentMethodName', TextareaType::class, [
                'required' => $enableRequiredFields,
            ])
            ->end();

        $form
            ->with('optional', [
                'label' => 'app.pm_payment_service.groups.optional',
                'class' => 'col-md-12',
                'description' => 'app.pm_payment_service.groups.optional_description',
            ]);
        $form
            ->add('paymentMethodPrefix', TextareaType::class, [
                'required' => false,
            ])
            ->add('paymentMethodStartNr', IntegerType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group-number'],
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('paymentMethodEndNr', IntegerType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group-number'],
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->end();
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('paymentMethodStartNr')
                ->addConstraint(new GreaterThanOrEqual([
                    'value' => 0,
                ]))
            ->end()
            ->with('paymentMethodStartNr')
                ->addConstraint(new GreaterThanOrEqual([
                    'value' => 0,
                ]))
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

        /** @var PmPaymentService $subject */
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

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'pmPayment');
        $this->addDefaultDatagridFilter($filter, 'solution');
        /*$filter->add('description');*/
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('pmPayment', null, [
                'admin_code' => PmPaymentAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'communeName'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'pmPayment'],
                ]
            ])
            ->add('solution', null, [
                'admin_code' => OnboardingServiceAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'solution'],
                ]
            ]);
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('pmPayment', null, [
                'admin_code' => PmPaymentAdmin::class
            ])
            ->add('solution', null, [
                'admin_code' => OnboardingServiceAdmin::class
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
