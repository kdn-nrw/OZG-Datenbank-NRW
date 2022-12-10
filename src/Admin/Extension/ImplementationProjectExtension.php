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

declare(strict_types=1);

namespace App\Admin\Extension;

use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Entity\ImplementationProject;
use App\Entity\StateGroup\CommuneType;
use App\Service\InjectAdminHelperTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Admin extension for configuring routes in the frontend
 */
class ImplementationProjectExtension extends AbstractAdminExtension
{
    use InjectAdminHelperTrait;

    /**
     * @var ModelManagerInterface
     */
    private $modelManager;

    /**
     * @phpstan-param DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $admin = $filter->getAdmin();
        $this->modelManager = $admin->getModelManager();
        $isFrontend = $admin instanceof AbstractFrontendAdmin;
        if (!$filter->has('name')) {
            $filter->add('name');
        }
        $modelClass = $admin->getClass();
        $helper = $this->adminHelper;
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'laboratories');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'solutions');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'serviceSystems');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'services.service');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'serviceSystems.situation.subject');
        if ($isFrontend) {
            $filter->add('status');

        } else {
            $helper->addDefaultDatagridFilter($modelClass, $filter, 'status');
        }
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'projectStartAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'conceptStatusAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'implementationStatusAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'pilotingStatusAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'commissioningStatusAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'nationwideRolloutAt');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'projectLeaders');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'participationOrganisations');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'interestedOrganisations');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'fundings');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'services.service.bureaus');
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'services.service.portals', ['label' => 'app.implementation_project.entity.portals']);
        $helper->addDefaultDatagridFilter($modelClass, $filter, 'solutions.openDataItems');
        if (!$isFrontend) {
            $helper->addDefaultDatagridFilter($modelClass, $filter, 'fimExperts');
            $helper->addDefaultDatagridFilter($modelClass, $filter, 'contacts');
        }
        $this->addCommuneTypesFilter($filter);
        $filter
            ->add('efaType', ChoiceFilter::class, [
                'label' => 'app.implementation_project.entity.efa_type',
                'field_options' => [
                    'choices' => array_flip(ImplementationProject::EFA_TYPES),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'choice_translation_domain' => 'messages',
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    /**
     * Add- custom query condition for full text data grid filter field
     * @param DatagridMapper $filter
     */
    private function addCommuneTypesFilter(DatagridMapper $filter): void
    {
        //$helper->addDefaultDatagridFilter($modelClass, $filter, 'services.service.communeTypes', ['label' => 'app.service_system.entity.commune_types']);
        $filter->add('communeTypes',
            CallbackFilter::class, [
                'label' => 'app.service_system.entity.commune_types',
                'callback' => function (ProxyQueryInterface $queryBuilder, $alias, $field, FilterData $data) {
                    if (!$data->hasValue()) {
                        return false;
                    }
                    $dataValue = $data->getValue();
                    if ($dataValue instanceof Collection && $dataValue->count() > 0) {
                        /** @var ModelManager $modelManager */
                        $modelManager = $this->modelManager;
                        $connection = $modelManager->getEntityManager(CommuneType::class)->getConnection();
                        $entityIds = [];
                        foreach ($dataValue as $entity) {
                            /** @var CommuneType $entity */
                            $entityIds[] = $entity->getId();
                        }
                        $query = 'SELECT ips.implementation_project_id FROM ozg_implementation_project_service ips, ozg_service_commune_type sct WHERE sct.commune_type_id IN (?) AND sct.service_id = ips.service_id GROUP BY ips.implementation_project_id';
                        $result = $connection->fetchFirstColumn($query, [$entityIds], [Connection::PARAM_INT_ARRAY]);
                        if (!empty($result)) {
                            $queryBuilder->andWhere($queryBuilder->expr()->in(sprintf('%s.%s', $alias, 'id'), $result));
                        }
                        return true;
                    }
                    return false;
                },
                'field_type' => ModelType::class,
                'field_options' => [
                    'model_manager' => $this->modelManager,
                    'class' => CommuneType::class,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false
                ],
            ],
            [
                'multiple' => true,
                'expanded' => false,
            ]
        );
    }
}
