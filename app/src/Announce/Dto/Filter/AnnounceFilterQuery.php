<?php

namespace App\Announce\Dto\Filter;

use App\Shared\Api\Doctrine\Filter\Adapter\FilterQueryDefinitionInterface;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\Api\Doctrine\Filter\Operator\ComparisonOperator\ContainOperator;

class AnnounceFilterQuery implements FilterQueryDefinitionInterface
{
    public function definition(): FilterDefinitionBag
    {
        return new FilterDefinitionBag()
            ->add(FilterDefinition::create('title', 'title', operators: [ContainOperator::class]));
    }
}
