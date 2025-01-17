<?php

declare(strict_types=1);

namespace App\Tests\Shared\Api\Doctrine\Filter\Operator;

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
use App\Shared\Api\Doctrine\Filter\Operator\OperatorInterface;
use App\Shared\Api\Doctrine\Filter\Operator\StartWithOperator;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(OperatorInterface::class)]
#[CoversClass(BetweenOperator::class)]
#[CoversClass(ContainOperator::class)]
#[CoversClass(EndWithOperator::class)]
#[CoversClass(EqualOperator::class)]
#[CoversClass(GreaterThanOperator::class)]
#[CoversClass(GreaterThanOrEqualOperator::class)]
#[CoversClass(InOperator::class)]
#[CoversClass(IsEmptyOperator::class)]
#[CoversClass(IsNullOperator::class)]
#[CoversClass(LowerThanOperator::class)]
#[CoversClass(LowerThanOrEqualOperator::class)]
#[CoversClass(NotBetweenOperator::class)]
#[CoversClass(NotContainOperator::class)]
#[CoversClass(NotEndWithOperator::class)]
#[CoversClass(NotEqualOperator::class)]
#[CoversClass(NotInOperator::class)]
#[CoversClass(NotStartWithOperator::class)]
#[CoversClass(StartWithOperator::class)]
final class OperatorsTest extends TestCase
{
    #[DataProvider('provideOperators')]
    public function testOperators(
        string $fqcn,
        string $operator,
        array $wheres,
        string $value,
        array $params = [],
    ): void {
        /** @var OperatorInterface $instance */
        $instance = new $fqcn();

        self::assertSame($operator, $instance::operator());

        $qb = new QueryBuilder($this->createMock(EntityManagerInterface::class));
        $qb
            ->select('*')
            ->from(User::class, 'e')
        ;

        $filterDefinition = FilterDefinition::create(
            field: 'entityField',
            publicName: 'publicName',
            operators: [$fqcn]
        );

        $instance->apply($qb, $filterDefinition, $value);
        $whereParts = $qb->getDQLPart('where')->getParts();

        self::assertEquals($wheres, $whereParts);
        foreach ($qb->getParameters() as $index => $parameter) {
            self::assertSame($parameter->getValue(), $params[$index]);
        }
    }

    public function provideOperators(): iterable
    {
        yield [
            'fqcn' => BetweenOperator::class,
            'operator' => 'btw',
            'wheres' => ['e.entityField BETWEEN :param1 AND :param2'],
            'value' => 'value1,value2',
            'params' => ['value1', 'value2'],
        ];

        yield [
            'fqcn' => ContainOperator::class,
            'operator' => 'ctn',
            'wheres' => ['e.entityField LIKE :param1'],
            'value' => 'value',
            'params' => ['%value%'],
        ];

        yield [
            'fqcn' => EndWithOperator::class,
            'operator' => 'end',
            'wheres' => ['e.entityField LIKE :param1'],
            'value' => 'value',
            'params' => ['%value'],
        ];

        yield [
            'fqcn' => EqualOperator::class,
            'operator' => 'eq',
            'wheres' => [new Comparison('e.entityField', Comparison::EQ, ':param1')],
            'value' => 'value',
            'params' => ['value'],
        ];

        yield [
            'fqcn' => GreaterThanOperator::class,
            'operator' => 'gt',
            'wheres' => ['e.entityField > :param1'],
            'value' => 'value',
            'params' => ['value'],
        ];

        yield [
            'fqcn' => GreaterThanOrEqualOperator::class,
            'operator' => 'gte',
            'wheres' => ['e.entityField >= :param1'],
            'value' => 'value',
            'params' => ['value'],
        ];

        yield [
            'fqcn' => InOperator::class,
            'operator' => 'in',
            'wheres' => ['e.entityField IN (:param1)'],
            'value' => 'value,value2',
            'params' => [['value', 'value2']],
        ];

        yield [
            'fqcn' => IsEmptyOperator::class,
            'operator' => 'empty',
            'wheres' => ["e.entityField = '' OR e.entityField = 0"],
            'value' => 'true',
            'params' => [],
        ];

        yield [
            'fqcn' => IsEmptyOperator::class,
            'operator' => 'empty',
            'wheres' => ["e.entityField != '' OR e.entityField != 0"],
            'value' => 'false',
            'params' => [],
        ];

        yield [
            'fqcn' => IsNullOperator::class,
            'operator' => 'isnull',
            'wheres' => ['e.entityField IS NULL'],
            'value' => 'true',
            'params' => [],
        ];

        yield [
            'fqcn' => IsNullOperator::class,
            'operator' => 'isnull',
            'wheres' => ['e.entityField IS NOT NULL'],
            'value' => 'false',
            'params' => [],
        ];

        yield [
            'fqcn' => LowerThanOperator::class,
            'operator' => 'lt',
            'wheres' => ['e.entityField < :param1'],
            'value' => 'value',
            'params' => ['value'],
        ];

        yield [
            'fqcn' => LowerThanOrEqualOperator::class,
            'operator' => 'lte',
            'wheres' => ['e.entityField <= :param1'],
            'value' => 'value',
            'params' => ['value'],
        ];

        yield [
            'fqcn' => NotBetweenOperator::class,
            'operator' => 'nbtw',
            'wheres' => ['e.entityField NOT BETWEEN :param1 AND :param2'],
            'value' => 'value1,value2',
            'params' => ['value1', 'value2'],
        ];

        yield [
            'fqcn' => NotContainOperator::class,
            'operator' => 'nctn',
            'wheres' => ['e.entityField NOT LIKE :param1'],
            'value' => 'value1',
            'params' => ['%value1%'],
        ];

        yield [
            'fqcn' => NotEndWithOperator::class,
            'operator' => 'nend',
            'wheres' => ['e.entityField NOT LIKE :param1'],
            'value' => 'value1',
            'params' => ['%value1'],
        ];

        yield [
            'fqcn' => NotEqualOperator::class,
            'operator' => 'neq',
            'wheres' => [new Comparison('e.entityField', Comparison::NEQ, ':param1')],
            'value' => 'value1',
            'params' => ['value1'],
        ];

        yield [
            'fqcn' => NotInOperator::class,
            'operator' => 'nin',
            'wheres' => ['e.entityField NOT IN (:param1)'],
            'value' => 'value1,value2',
            'params' => [['value1', 'value2']],
        ];

        yield [
            'fqcn' => NotStartWithOperator::class,
            'operator' => 'nstw',
            'wheres' => ['e.entityField NOT LIKE :param1'],
            'value' => 'value1',
            'params' => ['value1%'],
        ];

        yield [
            'fqcn' => StartWithOperator::class,
            'operator' => 'stw',
            'wheres' => ['e.entityField LIKE :param1'],
            'value' => 'value1',
            'params' => ['value1%'],
        ];
    }
}
