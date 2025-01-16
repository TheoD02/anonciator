<?php

declare(strict_types=1);

namespace App\Announce\Dto\Filter;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\BetweenOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\ContainOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\EqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\GreaterThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\GreaterThanOrEqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\LowerThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\LowerThanOrEqualOperator;

class AnnounceFilterQuery implements FilterQueryDefinitionInterface
{
    public function definition(): FilterDefinitionBag
    {
        return new FilterDefinitionBag()
            ->add(FilterDefinition::create('title', 'title', operators: [ContainOperator::class]))
            ->add(FilterDefinition::create(
                field: 'category',
                publicName: 'categoryId',
                operators: [EqualOperator::class],
            ))
            ->add(
                FilterDefinition::create('price', 'price', operators: [
                    GreaterThanOperator::class,
                    LowerThanOperator::class,
                    GreaterThanOrEqualOperator::class,
                    LowerThanOrEqualOperator::class,
                    BetweenOperator::class,
                ])
            );
    }
}
