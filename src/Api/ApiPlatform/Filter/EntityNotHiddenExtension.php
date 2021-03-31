<?php

namespace App\Api\ApiPlatform\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Base\HideableEntityInterface;
use Doctrine\ORM\QueryBuilder;

class EntityNotHiddenExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!is_a($resourceClass, HideableEntityInterface::class, true)) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.hidden = :hidden', $rootAlias))
            ->setParameter('hidden', false);
    }
}