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

namespace App\Admin\Traits;

use App\Admin\OrganisationAdmin;
use App\Entity\Manufacturer;
use App\Entity\Organisation;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\MinistryState;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 *
 * Trait OrganisationTrait
 * @package App\Admin\Traits
 * @property \Sonata\AdminBundle\Model\ModelManagerInterface $modelManager
 */
trait OrganisationTrait
{
    protected function addOrganisationsFormFields(
        FormMapper $formMapper,
                   $fieldName = 'organisations',
        ?array     $filterChoices = null
    )
    {
        $isPlural = substr($fieldName, -1) === 's';
        $options = [
            'btn_add' => false,//'app.common.model_list_type.add',
            'property' => 'name',
            'placeholder' => '',
            'required' => false,
            'multiple' => $isPlural,
            'by_reference' => false,
            'btn_catalogue' => 'messages',
        ];
        $queryBuilder = $this->getOrganisationQueryBuilder($filterChoices);
        if (null !== $queryBuilder) {
            $options['query'] = $queryBuilder;
            $formMapper->add($fieldName, ModelType::class, $options, [
                    'admin_code' => OrganisationAdmin::class,
                ]
            );
        } else {
            $formMapper->add($fieldName, ModelAutocompleteType::class, $options, [
                    'admin_code' => OrganisationAdmin::class,
                ]
            );
        }
    }


    /**
     * Returns the query builder for the organisation form type
     *
     * @param array|null $filterChoices List referenced entity types to be displayed
     *
     * @return QueryBuilder|null
     */
    protected function getOrganisationQueryBuilder(?array $filterChoices = null): ?QueryBuilder
    {
        $queryBuilder = null;
        if (null !== $filterChoices && array_key_exists('entityTypes', $filterChoices)) {
            $allowedEntityTypes = $filterChoices['entityTypes'];
            /** @var EntityManager $em */
            $em = $this->modelManager->getEntityManager(ServiceProvider::class);

            $queryBuilder = $em->createQueryBuilder()
                ->select('o')
                ->from(Organisation::class, 'o');
            $orConditions = [];
            $mapEntityTypes = [
                Commune::class => 'commune',
                Manufacturer::class => 'manufacturer',
                ServiceProvider::class => 'serviceProvider',
                MinistryState::class => 'ministryState',
            ];
            foreach ($mapEntityTypes as $entityClass => $orgProperty) {
                if (in_array($entityClass, $allowedEntityTypes, false)) {
                    $orConditions[] = $queryBuilder->expr()->isNotNull('o.' . $orgProperty);
                }
            }
            if (!empty($orConditions)) {
                $queryBuilder->andWhere($queryBuilder->expr()->orX()->addMultiple($orConditions));
            }
            $queryBuilder->addOrderBy('o.name', 'ASC');
        }
        return $queryBuilder;
    }

    protected function addOrganisationsListFields(ListMapper $listMapper, $fieldName = 'organisations')
    {
        $listMapper
            ->add($fieldName, null, [
                'template' => 'General/Association/list_many_to_many_nolinks.html.twig',
                'admin_code' => OrganisationAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addOrganisationsShowFields(ShowMapper $showMapper, $fieldName = 'organisations')
    {
        $showMapper
            ->add($fieldName, null, [
                'admin_code' => OrganisationAdmin::class,
            ]);
    }
}