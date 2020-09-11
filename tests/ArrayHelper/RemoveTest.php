<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

class RemoveTest extends TestCase
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
     *
     * @param string|array $key
     * @param mixed $default
     * @param mixed $expectedValue
     * @param array $expectedArray
     */
    public function testRemove($key, $default, $expectedValue, array $expectedArray): void
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
     *
     * @param string|array $key
     * @param mixed $default
     * @param mixed $expectedValue
     * @param array $expectedArray
     */
    public function testRemoveByPath($key, $default, $expectedValue, array $expectedArray): void
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
     *
     * @param string|array $key
     * @param mixed $default
     * @param mixed $expectedValue
     * @param array $expectedArray
     */
    public function testRemoveByPathWithCustomDelimiter($key, $default, $expectedValue, array $expectedArray): void
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
