<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Arrays\ArrayHelper;

final class IndexAndRemoveKeyTest extends TestCase
{
    public function testSimple(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];

        $result = ArrayHelper::indexAndRemoveKey($array, 'id');

        $this->assertSame(
            [
                '123' => ['data' => 'abc'],
                '345' => ['data' => 'ghi'],
            ],
            $result
        );
    }

    public function testWithElementsWithoutKey(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['data' => 'ghi'],
        ];

        $expected = [
            123 => ['data' => 'abc'],
            345 => ['data' => 'def'],
        ];

        $result = ArrayHelper::indexAndRemoveKey($array, 'id');

        $this->assertSame($expected, $result);
    }

    public function testSimpleGroupBy(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];

        $expected = [
            '123' => [
                'abc' => ['id' => '123'],
            ],
            '345' => [
                'def' => ['id' => '345'],
                'ghi' => ['id' => '345'],
            ],
        ];

        $this->assertSame($expected, ArrayHelper::indexAndRemoveKey($array, 'data', ['id']));
        $this->assertSame($expected, ArrayHelper::indexAndRemoveKey($array, 'data', 'id'));
    }

    public function testStringElement(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            'data',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('indexAndRemoveKey() can not get value from string. The $array should be either multidimensional array.');
        ArrayHelper::indexAndRemoveKey($array, 'id');
    }

    public function testObjectElement(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            new stdClass(),
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('indexAndRemoveKey() can not get value from stdClass. The $array should be either multidimensional array.');
        ArrayHelper::indexAndRemoveKey($array, 'id');
    }

    public function testGroupByWithKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::indexAndRemoveKey(['id' => '1'], 'id', ['id']);
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/11739
     */
    public function te1stIndexFloat(): void
    {
        $array = [
            ['id' => 1e6, 'data' => 'a'],
            ['id' => 1e32, 'data' => 'b'],
            ['id' => 1e64, 'data' => 'c'],
            ['id' => 1465540807.522109, 'data' => 'd'],
        ];

        $expected = [
            '1000000' => ['data' => 'a'],
            '1.0E+32' => ['data' => 'b'],
            '1.0E+64' => ['data' => 'c'],
            '1465540807.5221' => ['data' => 'd'],
        ];

        $result = ArrayHelper::index($array, 'id');

        $this->assertEquals($expected, $result);
    }
}
