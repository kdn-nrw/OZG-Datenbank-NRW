<?php

namespace App\Api\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
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

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!$this->supports($resourceClass)) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.hidden = :hidden', $rootAlias))
            ->setParameter('hidden', false);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        if (!$this->supports($resourceClass)) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.hidden = :hidden', $rootAlias))
            ->setParameter('hidden', false);
    }
}