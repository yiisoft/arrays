<?php

declare(strict_types=1);

namespace ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class RenameKeyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'base' => [
            ['new-name' => 'asc', 'age' => 'desc'],
            ['name' => 'asc', 'age' => 'desc'],
            'name',
            'new-name',
        ];
        yield 'not-exist' => [
            ['age' => 'desc', 'name' => 'asc'],
            ['age' => 'desc', 'name' => 'asc'],
            'a',
            'b',
        ];
        yield 'empty' => [
            [],
            [],
            'name',
            'new-name',
        ];
        yield 'equal-names' => [
            ['name' => 'asc', 'age' => 'desc'],
            ['name' => 'asc', 'age' => 'desc'],
            'name',
            'name',
        ];
        yield 'int-keys' => [
            [0 => 'a', 4 => 'b', 2 => 'c'],
            ['a', 'b', 'c'],
            1,
            4,
        ];
        yield 'null-values' => [
            [0 => 'a', 4 => null, 2 => 'c'],
            ['a', null, 'c'],
            1,
            4,
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(array $expected, array $array, string|int $from, string|int $to): void
    {
        $result = ArrayHelper::renameKey($array, $from, $to);

        $this->assertSame($expected, $result);
    }
}
