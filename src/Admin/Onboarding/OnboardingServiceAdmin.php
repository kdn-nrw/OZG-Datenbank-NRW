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

use App\Admin\ServiceSystemAdmin;
use App\Admin\SolutionAdmin;
use App\Entity\Solution;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;


class OnboardingServiceAdmin extends SolutionAdmin
{
    protected $baseRouteName = 'admin_app_onboarding_onboarding_service';
    protected $baseRoutePattern = 'onboarding/onboarding-service';

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        // Fix Sonata-Bug https://github.com/sonata-project/SonataAdminBundle/issues/3368
        // When global search is executed, the filter query will be concatenated with the additional
        // conditions in this function with OR (instead of AND)
        // This means all extra conditions will be ignored and we have to execute the full search query here
        // @see \Sonata\AdminBundle\Search\SearchHandler::search
        $reqSearchTerm = null;
        if ($this->hasRequest()) {
            /** @noinspection NullPointerExceptionInspection */
            $reqSearchTerm = $this->getRequest()->get('q');
        } elseif (isset($_REQUEST['q'])) {
            $reqSearchTerm = $_REQUEST['q'];
        }
        /** @var \Doctrine\ORM\QueryBuilder $query */
        if ($reqSearchTerm) {
            $searchTerm = strtolower(trim(strip_tags($reqSearchTerm)));
            /** @var \Doctrine\ORM\QueryBuilder $subQueryBuilder */
            $subQueryBuilder = $this->getModelManager()->createQuery(Solution::class, 's');
            $subQueryBuilder->select('s.id')
                ->where(
                    $subQueryBuilder->expr()->andX(
                        's.enabledMunicipalPortal = :enabledMunicipalPortal',
                        //'s.communeType = :communeType',
                        's.name LIKE :term'
                    )
                );
            $subQueryBuilder->setParameter('enabledMunicipalPortal', 1);
            //$subQueryBuilder->setParameter('communeType', Solution::COMMUNE_TYPE_ALL);
            $subQueryBuilder->setParameter('term', '%' . $searchTerm . '%');
            $result = $subQueryBuilder->getQuery()->getArrayResult();
            if (!empty($result)) {
                $idList = array_column($result, 'id');
            } else {
                $idList = [0];
            }
            $query->andWhere(
                $query->getRootAliases()[0] . ' IN (:idList)'
            );
            $query->setParameter('idList', $idList);
        } else {
            $rootAlias = $query->getRootAliases()[0];
            $query->andWhere(
                $query->expr()->andX(
                    $rootAlias . '.enabledMunicipalPortal = :enabledMunicipalPortal'
                //$rootAlias . '.communeType = :communeType'
                )
            );
            //$query->setParameter('communeType', Solution::COMMUNE_TYPE_ALL);
            $query->setParameter('enabledMunicipalPortal', 1);
        }
        return $query;
    }

    /**
     * @return mixed
     */
    public function getTranslatorNamingPrefix()
    {
        if (null === $this->translatorNamingPrefix) {
            $this->translatorNamingPrefix = SolutionAdmin::class;
        }
        return $this->translatorNamingPrefix;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('serviceProviders', null, [
                'template' => 'SolutionAdmin/list-service-providers.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceProviders'],
                ]
            ])
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-service-systems.html.twig',
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSolutions'],
                    ['fieldName' => 'service'],
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('jurisdictions', 'string', [
                'label' => 'app.service_system.entity.jurisdictions',
                //'associated_property' => 'name',
                'template' => 'SolutionAdmin/list-jurisdiction.html.twig',
                'enable_filter_add' => true,
            ])
            ->add('name')/*
            ->add('status', TemplateRegistryInterface::TYPE_CHOICE, [
                'editable' => true,
                'class' => Status::class,
                'catalogue' => 'messages',
            ])*/
            ->add('maturity', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'maturity'],
                ],
                'enable_filter_add' => true,
            ])
            ->add('url', 'url');
        $this->addDefaultListActions($list);
    }
}
