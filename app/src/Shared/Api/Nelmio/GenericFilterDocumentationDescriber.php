<?php

declare(strict_types=1);

namespace App\Shared\Api\Nelmio;

use App\Shared\Api\Doctrine\Filter\Documentation\OperatorDescriber;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberInterface;
use Nelmio\ApiDocBundle\RouteDescriber\RouteDescriberTrait;
use OpenApi\Annotations\OpenApi;
use OpenApi\Attributes\Parameter;
use OpenApi\Generator;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\Routing\Route;

#[AsTaggedItem('nelmio_api_doc.route_describer')]
class GenericFilterDocumentationDescriber implements RouteDescriberInterface
{
    use RouteDescriberTrait;

    /**
     * @var array<OperatorDescriber>
     */
    private static array $parameters = [];

    public function describe(OpenApi $api, Route $route, \ReflectionMethod $reflectionMethod): void
    {
        $operations = $this->getOperations($api, $route);

        foreach ($operations as $operation) {
            /** @var array<Parameter> $parameters */
            $parameters = $operation->parameters === Generator::UNDEFINED ? [] : $operation->parameters;

            $parameters[] = self::$parameters['include'] ??= new Parameter(
                name: 'include',
                description: 'Include related resources',
                in: 'query',
                example: 'relation1,relation2',
            );

            $parameters[] = self::$parameters['only'] ??= new Parameter(
                parameter: 'only',
                name: 'only',
                description: 'Include only specified fields',
                in: 'query',
                example: 'property1,property2',
            );

            $parameters[] = self::$parameters['ignore'] ??= new Parameter(
                parameter: 'IgnoreArray',
                name: 'ignore',
                description: 'Ignore specified fields',
                in: 'query',
                example: 'property1,property2',
            );

            $operation->parameters = $parameters;
        }
    }
}
