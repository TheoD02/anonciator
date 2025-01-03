<?php

declare(strict_types=1);

namespace App\Shared\Api\Doctrine\Filter\Adapter;

use App\Shared\Api\Doctrine\Filter\FilterDefinitionBag;
use App\Shared\FilterQueryInterface;

interface FilterQueryDefinitionInterface extends FilterQueryInterface
{
    public function definition(): FilterDefinitionBag;
}
