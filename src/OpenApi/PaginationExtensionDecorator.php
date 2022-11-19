<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaginationExtensionDecorator
 * Process request values for pagination to ensure valid values
 *
 * @package App\OpenApi
 */
final class PaginationExtensionDecorator implements QueryResultCollectionExtensionInterface
{
    /**
     * @var PaginationExtension
     */
    private $decorated;
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * PaginationExtensionDecorator constructor.
     * @param RequestStack $requestStack
     * @param PaginationExtension $decorated
     */
    public function __construct(
        RequestStack $requestStack,
        PaginationExtension $decorated
    )
    {
        $this->decorated = $decorated;
        $this->requestStack = $requestStack;
    }

    public function supportsResult(string $resourceClass, Operation $operation = null, array $context = []): bool
    {
        return $this->decorated->supportsResult($resourceClass, $operation, $context);
    }

    public function getResult(QueryBuilder $queryBuilder, string $resourceClass = null, Operation $operation = null, array $context = [])
    {
        return $this->decorated->getResult($queryBuilder, $resourceClass, $operation, $context);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
//        if (!empty($context['filters']) && array_key_exists('page', $context['filters'])) {
//            $requestedValue = (int) $context['filters']['page'];
//            $processedValue = $this->forceIntegerInRange($requestedValue, 1, 99999);
//            if ($processedValue !== $requestedValue) {
//                $context['filters']['page'] = $processedValue;
//            }
//        }
//        // Prevent InvalidArgumentException for pagination query parameters
//        if (null !== $this->requestStack && null !== $request = $this->requestStack->getCurrentRequest()) {
//            // ['arg_name' => 'pageParameterName', 'type' => 'string', 'default' => 'page'],
//            // ['arg_name' => 'enabledParameterName', 'type' => 'string', 'default' => 'pagination'],
//            // ['arg_name' => 'itemsPerPageParameterName', 'type' => 'string', 'default' => 'itemsPerPage'],
//            $this->fixPaginationParameter($request, 'page', 1, 99999);
//            $this->fixPaginationParameter($request, 'itemsPerPage', 1, 50);
//        }
        $this->decorated->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }

    /*
    private function fixPaginationParameter(Request $request, string $parameterName, $min = 1, $max = null)
    {
        if (null !== $paginationAttribute = $request->attributes->get('_api_pagination')) {
            if (array_key_exists($parameterName, $paginationAttribute)) {
                $requestedValue = (int)$paginationAttribute[$parameterName];
                $processedValue = $this->forceIntegerInRange($requestedValue, $min, $max);
                if ($processedValue !== $requestedValue) {
                    $paginationAttribute[$parameterName] = $processedValue;
                    $request->attributes->set('_api_pagination', $paginationAttribute);
                }
            }
        } elseif ($request->query->has($parameterName)) {
            $requestedValue = (int)$request->query->get($parameterName);
            $processedValue = $this->forceIntegerInRange($requestedValue, $min, $max);
            if ($processedValue !== $requestedValue) {
                $request->query->set($parameterName, $processedValue);
            }
        }
    }*/

    /**
     * Ensure that the given value is inside the defined number range
     * @param int $value
     * @param int $min
     * @param int|null $max
     * @return int|null
     * /
    private function forceIntegerInRange(int $value, int $min, ?int $max): ?int
    {
        $processedValue = $value;
        if ($value < $min) {
            $processedValue = $min;
        } elseif (null !== $max && $value > $max) {
            $processedValue = $max;
        }
        return $processedValue;
    }*/

}