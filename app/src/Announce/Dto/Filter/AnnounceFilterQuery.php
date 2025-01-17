<?php

declare(strict_types=1);

namespace App\Announce\Dto\Filter;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\Api\Doctrine\Filter\Operator\BetweenOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ContainOperator;
use App\Shared\Api\Doctrine\Filter\Operator\EqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOrEqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\LowerThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\LowerThanOrEqualOperator;

class AnnounceFilterQuery implements FilterQueryDefinitionInterface
{
    public function definition(): FilterDefinitionBag
    {
        return new FilterDefinitionBag()
            ->add(
                FilterDefinition::create(field: 'title', publicName: 'title', operators: [ContainOperator::class])
            )
            ->add(FilterDefinition::create(
                field: 'category',
                publicName: 'categoryId',
                operators: [EqualOperator::class],
            ))
            ->add(
                FilterDefinition::create(
                    field: 'price',
                    publicName: 'price',
                    operators: [
                        GreaterThanOperator::class,
                        LowerThanOperator::class,
                        GreaterThanOrEqualOperator::class,
                        LowerThanOrEqualOperator::class,
                        BetweenOperator::class,
                    ]
                )
            )
        ;
    }
}
