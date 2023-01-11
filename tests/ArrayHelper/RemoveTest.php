<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class RemoveTest extends TestCase
{
    public function removeData(): array
    {
        return [
            [
                'name',
                null,
                'Dmitry',
                ['age' => 42],
            ],
            [
                ['name'],
                null,
                'Dmitry',
                ['age' => 42],
            ],
            [
                'sex',
                'male',
                'male',
                ['name' => 'Dmitry', 'age' => 42],
            ],
            [
                ['name', 'firstName'],
                null,
                null,
                ['name' => 'Dmitry', 'age' => 42],
            ],
        ];
    }

    /**
     * @dataProvider removeData
     */
    public function testRemove(array|string $key, mixed $default, mixed $expectedValue, array $expectedArray): void
    {
        $array = [
            'name' => 'Dmitry',
            'age' => 42,
        ];

        $name = ArrayHelper::remove($array, $key, $default);

        $this->assertEquals($expectedValue, $name);
        $this->assertEquals($expectedArray, $array);
    }

    public function testRemoveNested(): void
    {
        $array = [
            'name' => [
                'firstName' => 'Dmitry',
            ],
            'age' => 42,
        ];

        $name = ArrayHelper::remove($array, ['name', 'firstName']);

        $this->assertEquals('Dmitry', $name);
        $this->assertEquals(['name' => [], 'age' => 42], $array);
    }

    public function testRemoveByIntKey(): void
    {
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];

        $value = ArrayHelper::remove($array, 2);

        $this->assertSame('b', $value);
        $this->assertSame([1 => 'a', 3 => 'c'], $array);
    }

    public function dataRemoveByFloatKey(): array
    {
        return [
            [
                [1 => 'a', 2 => 'b', 3 => 'c'],
                2.0,
                'b',
                [1 => 'a', 3 => 'c'],
            ],
            [
                [1 => 'a', '2.01' => 'b', 3 => 'c'],
                2.01,
                'b',
                [1 => 'a', 3 => 'c'],
            ],
        ];
    }

    /**
     * @dataProvider dataRemoveByFloatKey
     */
    public function testRemoveByFloatKey(array $array, float $key, string $expectedValue, array $expectedArray): void
    {
        $value = ArrayHelper::remove($array, $key);

        $this->assertSame($expectedValue, $value);
        $this->assertSame($expectedArray, $array);
    }

    public function removeByPathData(): array
    {
        return [
            [
                'name.firstName',
                null,
                'Dmitry',
                ['name' => [], 'age' => 42],
            ],
            [
                ['name.firstName'],
                null,
                'Dmitry',
                ['name' => [], 'age' => 42],
            ],
            [
                ['name', ['firstName']],
                null,
                'Dmitry',
                ['name' => [], 'age' => 42],
            ],
        ];
    }

    /**
     * @dataProvider removeByPathData
     */
    public function testRemoveByPath(array|string $key, mixed $default, mixed $expectedValue, array $expectedArray): void
    {
        $array = [
            'name' => [
                'firstName' => 'Dmitry',
            ],
            'age' => 42,
        ];

        $name = ArrayHelper::removeByPath($array, $key, $default);

        $this->assertEquals($expectedValue, $name);
        $this->assertEquals($expectedArray, $array);
    }

    public function removeByPathWithCustomDelimiterData(): array
    {
        return [
            [
                'name~firstName',
                null,
                'Dmitry',
                ['name' => [], 'age' => 42],
            ],
            [
                ['name~firstName'],
                null,
                'Dmitry',
                ['name' => [], 'age' => 42],
            ],
        ];
    }

    /**
     * @dataProvider removeByPathWithCustomDelimiterData
     */
    public function testRemoveByPathWithCustomDelimiter(array|string $key, mixed $default, mixed $expectedValue, array $expectedArray): void
    {
        $array = [
            'name' => [
                'firstName' => 'Dmitry',
            ],
            'age' => 42,
        ];

        $name = ArrayHelper::removeByPath($array, $key, $default, '~');

        $this->assertEquals($expectedValue, $name);
        $this->assertEquals($expectedArray, $array);
    }
}
