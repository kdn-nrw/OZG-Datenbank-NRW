<?php
// api/src/OpenApi/JwtDecorator.php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;

/**
 * Class JwtDecorator
 * @package App\OpenApi
 * @see https://api-platform.com/docs/core/jwt/#documenting-the-authentication-mechanism-with-swaggeropen-api
 */
final class JwtDecorator implements OpenApiFactoryInterface
{
    /**
     * @var OpenApiFactoryInterface
     */
    private $decorated;

    public function __construct(
        OpenApiFactoryInterface $decorated
    ) {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();
        if (null === $schemas) {
            $schemas = new \ArrayObject([]);
        }
        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'johndoe@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'apassword',
                ],
            ],
        ]);

//        $schemas = $openApi->getComponents()->getSecuritySchemes() ?? [];
//        $schemas['JWT'] = new \ArrayObject([
//            'type' => 'http',
//            'scheme' => 'bearer',
//            'bearerFormat' => 'JWT',
//        ]);

        $pathItem = new Model\PathItem(
            'JWT Token',
            null,
            null,
            null,
            null,
            new Model\Operation(
                'postCredentialsItem',
                ['Token'],
                [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                 'Get JWT token to login.',
                '',
                null,
                [],
                 new Model\RequestBody(
                     'Generate new JWT Token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ])
                )
            )
        );
        $openApi->getPaths()->addPath('/api/authentication_token', $pathItem);

        return $openApi;
    }
}