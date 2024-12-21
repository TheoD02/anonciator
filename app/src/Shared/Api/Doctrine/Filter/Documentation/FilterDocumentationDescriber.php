<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use Nelmio\ApiDocBundle\RouteDescriber\RouteArgumentDescriber\RouteArgumentDescriberInterface;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTaggedItem('nelmio_api_doc.route_argument_describer')]
class FilterDocumentationDescriber implements RouteArgumentDescriberInterface
{
    private static $parameterInstances;

    /**
     * @var array<OperatorDescriber>
     */
    private array $operators = [];

    /**
     * @param iterable<OperatorDescriber> $operatorDescribers
     */
    public function __construct(
        #[AutowireIterator(OperatorDescriber::class)]
        iterable $operatorDescribers,
        #[Autowire(param: 'kernel.debug')]
        private readonly bool $debug = false,
    ) {
        foreach ($operatorDescribers as $operatorDescriber) {
            $this->operators[$operatorDescriber->operator()] = $operatorDescriber;
        }
    }

    public function describe(ArgumentMetadata $argumentMetadata, OA\Operation $operation): void
    {
        if ($argumentMetadata->getAttributes(MapQueryString::class, ArgumentMetadata::IS_INSTANCEOF) === []) {
            return;
        }

        $classFqcn = $argumentMetadata->getType();

        if ($classFqcn === null) {
            return;
        }

        if (! is_subclass_of($classFqcn, FilterQueryDefinitionInterface::class)) {
            return;
        }

        $instance = new $classFqcn();
        $parameters = $operation->parameters === Generator::UNDEFINED ? [] : $operation->parameters;

        foreach ($instance->definition() as $definition) {
            foreach ($definition->operators as $operator) {
                $operatorDescriber = $this->operators[$operator] ?? null;

                if ($operatorDescriber === null) {
                    if ($this->debug) {
                        throw new \RuntimeException(\sprintf(
                            'Operator describer not found for operator "%s"',
                            $operator
                        ));
                    }

                    continue;
                }

                $parameter = self::$parameterInstances[$operator] ?? $operatorDescriber->parameter($definition);
                $parameters[] = $parameter;
            }
        }

        $operation->parameters = $parameters;
    }
}
