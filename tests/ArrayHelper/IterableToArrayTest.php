<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use ArrayIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

use function PHPUnit\Framework\assertSame;

final class IterableToArrayTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield [[1, 2, 3], [1, 2, 3]];
        yield [[1, 2, 3], new ArrayIterator([1, 2, 3])];
        yield [['key' => 'value'], ['key' => 'value']];
        yield [['key' => 'value'], new ArrayIterator(['key' => 'value'])];
    }

    #[DataProvider('dataBase')]
    public function testBase(array $expected, iterable $value): void
    {
        $result = ArrayHelper::iterableToArray($value);

        assertSame($expected, $result);
    }
}
