<?php

namespace App\Api\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Base\HideableEntityInterface;
use Doctrine\ORM\QueryBuilder;

class EntityNotHiddenExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    /**
     * @param string $resourceClass
     * @return bool
     */
    protected function supports(string $resourceClass): bool
    {
        return is_a($resourceClass, HideableEntityInterface::class, true);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (!$this->supports($resourceClass)) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.hidden = :hidden', $rootAlias))
            ->setParameter('hidden', false);
    }
}