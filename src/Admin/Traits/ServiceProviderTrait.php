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

use App\Admin\StateGroup\ServiceProviderAdmin;
use App\Entity\StateGroup\ServiceProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait ServiceProviderTrait
 * @package App\Admin\Traits
 * @property \Sonata\AdminBundle\Model\ModelManagerInterface $modelManager
 */
trait ServiceProviderTrait
{
    /**
     * @param FormMapper $formMapper
     * @param string $fieldName Use "serviceProvider" for single item choice type
     * @param string|null $filterChoices Use "paymentProvider" => only show epayment providers
     */
    protected function addServiceProvidersFormFields(
        FormMapper $formMapper,
        string $fieldName = 'serviceProviders',
        ?string $filterChoices = null
    ): void
    {
        $isPlural = substr($fieldName, -1) === 's';
        $options = [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => $isPlural,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ];
        $queryBuilder = $this->getServiceProviderQueryBuilder($filterChoices);
        if (null !== $queryBuilder) {
            $options['query'] = $this->getServiceProviderQueryBuilder($filterChoices);
        }
        $formMapper->add($fieldName, ModelType::class, $options);
    }

    protected function addServiceProvidersListFields(ListMapper $listMapper, string $fieldName = 'serviceProviders')
    {
        $listMapper
            ->add($fieldName, null, [
                'admin_code' => ServiceProviderAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addServiceProvidersShowFields(ShowMapper $showMapper, string $fieldName = 'serviceProviders')
    {
        $showMapper
            ->add($fieldName, null, [
                'admin_code' => ServiceProviderAdmin::class,
            ]);
    }


    /**
     * Returns the query builder for the status
     *
     * @param string|null $filterChoices Use "paymentProvider" => only show epayment providers
     *
     * @return QueryBuilder|null
     */
    protected function getServiceProviderQueryBuilder(?string $filterChoices = null): ?QueryBuilder
    {
        $queryBuilder = null;
        if ($filterChoices === 'paymentProvider') {
            /** @var EntityManager $em */
            $em = $this->modelManager->getEntityManager(ServiceProvider::class);

            $queryBuilder = $em->createQueryBuilder()
                ->select('s')
                ->from(ServiceProvider::class, 's')
                ->where('s.enablePaymentProvider = 1')
                ->addOrderBy('s.name', 'ASC');
        }
        return $queryBuilder;
    }
}