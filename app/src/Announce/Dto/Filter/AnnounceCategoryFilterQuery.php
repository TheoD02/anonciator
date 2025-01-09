<?php

declare(strict_types=1);

namespace App\Announce\Dto\Filter;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\EqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\InOperator;

class AnnounceCategoryFilterQuery implements FilterQueryDefinitionInterface
{
    public function definition(): FilterDefinitionBag
    {
        return new FilterDefinitionBag()
            ->add(
                FilterDefinition::create(
                    field: 'id',
                    publicName: 'id',
                    operators: [EqualOperator::class, InOperator::class]
                )
            )
        ;
    }
}
