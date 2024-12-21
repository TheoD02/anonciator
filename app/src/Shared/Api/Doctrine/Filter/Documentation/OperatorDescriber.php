<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use OpenApi\Attributes\Parameter;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(OperatorDescriber::class)]
interface OperatorDescriber
{
    /**
     * @return class-string
     */
    public static function operator(): string;

    public function parameter(FilterDefinition $definition): Parameter;
}
