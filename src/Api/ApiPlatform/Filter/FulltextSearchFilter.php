<?php
namespace App\Api\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Base\NamedEntityInterface;
use App\Search\Finder;
use Doctrine\ORM\QueryBuilder;

/**
 * Custom API search filter, for searching the full text index table
 *
 * @see \ApiPlatform\Doctrine\Orm\Filter\SearchFilter
 */
final class FulltextSearchFilter extends AbstractFilter implements SearchFilterInterface
{
    /**
     * @var Finder
     */
    protected $finder;

    public const FULL_TEXT_SEARCH_PROPERTY = 'search';


    /**
     * @required
     * @param Finder $finder
     */
    public function injectFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = [])
    {
        // otherwise filter is applied to order and page as well
        if ($property !== self::FULL_TEXT_SEARCH_PROPERTY) {
            return;
        }
        if (null !== $this->finder && (string)$value !== '') {
            $matchingRecordIds = $this->finder->findMatchingRecordIds($resourceClass, (string) $value);
            if (empty($matchingRecordIds)) {
                $matchingRecordIds = [0];
            }
            $parameterName = $queryNameGenerator->generateParameterName('matchingRecordIds'); // Generate a unique parameter name to avoid collisions with other filters
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $orConditions[] = sprintf('%s IN(:%s)', $rootAlias, $parameterName);
            $queryBuilder->setParameter($parameterName, $matchingRecordIds);

            // Name is already searched for in full text search
            if (is_a($resourceClass, NamedEntityInterface::class, true)) {
                $literal = '%' . strtolower($value) . '%';
                $orConditions[] = $queryBuilder->expr()->like(
                    'LOWER('.$rootAlias . '.name)',
                    $queryBuilder->expr()->literal($literal)
                );
            }

            $queryBuilder
                ->andWhere($queryBuilder->expr()->orX()->addMultiple($orConditions));
        }
    }

    protected function getProperties(): ?array
    {
        if (empty($this->properties)) {
            $this->properties = ['search'];
        }
        return $this->properties;
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $description[self::FULL_TEXT_SEARCH_PROPERTY] = [
            'property' => self::FULL_TEXT_SEARCH_PROPERTY,
            'type' => 'string',
            'required' => false,
            'swagger' => [
                'description' => 'Filter using full text search!',
                'name' => 'Volltextsuche',
                'type' => 'Will appear below the name in the Swagger documentation',
            ],
        ];

        return $description;
    }
}