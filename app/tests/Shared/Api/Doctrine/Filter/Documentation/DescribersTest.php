<?php

namespace App\Tests\Shared\Api\Doctrine\Filter\Documentation;

use App\Shared\Api\Doctrine\Filter\Documentation\BetweenDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\ContainDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\EndWithDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\EqualDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\GreaterThanDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\GreaterThanOrEqualDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\InOperatorDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\IsEmptyDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\IsNullDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\LowerThanDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\LowerThanOrEqualDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotBetweenDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotContainDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotEndWithDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotEqualOperatorDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotInOperatorDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\NotStartWithDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\OperatorDescriber;
use App\Shared\Api\Doctrine\Filter\Documentation\StartWithDescriber;
use App\Shared\Api\Doctrine\Filter\FilterDefinition;
use App\Shared\Api\Doctrine\Filter\Operator\BetweenOperator;
use App\Shared\Api\Doctrine\Filter\Operator\ContainOperator;
use App\Shared\Api\Doctrine\Filter\Operator\EndWithOperator;
use App\Shared\Api\Doctrine\Filter\Operator\EqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\GreaterThanOrEqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\InOperator;
use App\Shared\Api\Doctrine\Filter\Operator\IsEmptyOperator;
use App\Shared\Api\Doctrine\Filter\Operator\IsNullOperator;
use App\Shared\Api\Doctrine\Filter\Operator\LowerThanOperator;
use App\Shared\Api\Doctrine\Filter\Operator\LowerThanOrEqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotBetweenOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotContainOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotEndWithOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotEqualOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotInOperator;
use App\Shared\Api\Doctrine\Filter\Operator\NotStartWithOperator;
use App\Shared\Api\Doctrine\Filter\Operator\StartWithOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OperatorDescriber::class)]
#[CoversClass(BetweenDescriber::class)]
#[CoversClass(ContainDescriber::class)]
#[CoversClass(EndWithDescriber::class)]
#[CoversClass(EqualDescriber::class)]
#[CoversClass(GreaterThanDescriber::class)]
#[CoversClass(GreaterThanOrEqualDescriber::class)]
#[CoversClass(InOperator::class)]
#[CoversClass(IsEmptyDescriber::class)]
#[CoversClass(IsNullDescriber::class)]
#[CoversClass(LowerThanDescriber::class)]
#[CoversClass(LowerThanOrEqualDescriber::class)]
#[CoversClass(NotBetweenDescriber::class)]
#[CoversClass(NotContainDescriber::class)]
#[CoversClass(NotEndWithDescriber::class)]
#[CoversClass(NotEqualOperatorDescriber::class)]
#[CoversClass(NotInOperatorDescriber::class)]
#[CoversClass(NotStartWithDescriber::class)]
#[CoversClass(StartWithDescriber::class)]
class DescribersTest extends TestCase
{
    public static function describerProvider(): array
    {
        return [
            [new BetweenDescriber(), BetweenOperator::class, 'publicName[btw]', 'Between operator'],
            [new ContainDescriber(), ContainOperator::class, 'publicName[ctn]', 'Contain operator'],
            [new EndWithDescriber(), EndWithOperator::class, 'publicName[end]', 'End with operator'],
            [new EqualDescriber(), EqualOperator::class, 'publicName[eq]', 'Equal operator'],
            [new GreaterThanDescriber(), GreaterThanOperator::class, 'publicName[gt]', 'Greater than operator'],
            [new GreaterThanOrEqualDescriber(), GreaterThanOrEqualOperator::class, 'publicName[gte]', 'Greater than or equal operator'],
            [new IsEmptyDescriber(), IsEmptyOperator::class, 'publicName[empty]', 'Is empty operator'],
            [new IsNullDescriber(), IsNullOperator::class, 'publicName[isnull]', 'Is null operator'],
            [new LowerThanDescriber(), LowerThanOperator::class, 'publicName[lt]', 'Lower than operator'],
            [new LowerThanOrEqualDescriber(), LowerThanOrEqualOperator::class, 'publicName[lte]', 'Lower than or equal operator'],
            [new NotBetweenDescriber(), NotBetweenOperator::class, 'publicName[nbtw]', 'Not between operator'],
            [new NotContainDescriber(), NotContainOperator::class, 'publicName[nctn]', 'Not contain operator'],
            [new NotEndWithDescriber(), NotEndWithOperator::class, 'publicName[nend]', 'Not end with operator'],
            [new NotEqualOperatorDescriber(), NotEqualOperator::class, 'publicName[neq]', 'Not equal operator'],
            [new NotInOperatorDescriber(), NotInOperator::class, 'publicName[nin]', 'Not in operator'],
            [new NotStartWithDescriber(), NotStartWithOperator::class, 'publicName[nstw]', 'Not start with operator'],
            [new StartWithDescriber(), StartWithOperator::class, 'publicName[stw]', 'Start with operator'],
            [new InOperatorDescriber(), InOperator::class, 'publicName[in]', 'In operator'],
        ];
    }

    /**
     * @dataProvider describerProvider
     */
    public function testDescriber($instance, $expectedOperator, $expectedName, $expectedDescription): void
    {
        $this->assertSame($expectedOperator, $instance::operator());
        $parameter = $instance->parameter(FilterDefinition::create(
            'entityName',
            'publicName',
        ));
        $this->assertSame($expectedName, $parameter->name);
        $this->assertSame($expectedDescription, $parameter->description);
        $this->assertSame('query', $parameter->in);
    }
}
