<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

final class IndexTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            [
                [
                    '123' => ['id' => '123', 'data' => 'abc'],
                    '345' => ['id' => '345', 'data' => 'ghi'],
                ],
                'id',
            ],
            [
                [
                    'abc' => ['id' => '123', 'data' => 'abc'],
                    'def' => ['id' => '345', 'data' => 'def'],
                    'ghi' => ['id' => '345', 'data' => 'ghi'],
                ],
                static fn(array $element) => $element['data'],
            ],
            [
                [],
                null
            ],
            [
                [],
                static fn() => null,
            ],
            [
                [
                    '123' => ['id' => '123', 'data' => 'abc'],
                ],
                static fn(array $element) => $element['id'] === '345' ? null : $element['id'],
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(array $expected, $key): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];

        $this->assertSame($expected, ArrayHelper::index($array, $key));
        $this->assertSame($expected, ArrayHelper::index(new IterableObject($array), $key));
    }

    public function testNonExistsKey(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['data' => 'ghi'],
        ];

        $expected = [
            123 => ['id' => '123', 'data' => 'abc'],
            345 => ['id' => '345', 'data' => 'def'],
        ];

        $this->assertSame($expected, ArrayHelper::index($array, 'id'));
        $this->assertSame($expected, ArrayHelper::index(new IterableObject($array), 'id'));
    }

    public function dataIndexGroupBy(): array
    {
        return [
            [
                [
                    '123' => [
                        ['id' => '123', 'data' => 'abc'],
                    ],
                    '345' => [
                        ['id' => '345', 'data' => 'def'],
                        ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
                null,
                ['id'],
            ],
            [
                [
                    '123' => [
                        ['id' => '123', 'data' => 'abc'],
                    ],
                    '345' => [
                        ['id' => '345', 'data' => 'def'],
                        ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
                null,
                'id',
            ],
            [
                [
                    '123' => [
                        'abc' => [
                            ['id' => '123', 'data' => 'abc'],
                        ],
                    ],
                    '345' => [
                        'def' => [
                            ['id' => '345', 'data' => 'def'],
                        ],
                        'ghi' => [
                            ['id' => '345', 'data' => 'ghi'],
                        ],
                    ],
                ],
                null,
                ['id', 'data'],
            ],
            [
                [
                    '123' => [
                        'abc' => ['id' => '123', 'data' => 'abc'],
                    ],
                    '345' => [
                        'def' => ['id' => '345', 'data' => 'def'],
                        'ghi' => ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
                'data',
                ['id'],
            ],
            [
                [
                    '123' => [
                        'abc' => ['id' => '123', 'data' => 'abc'],
                    ],
                    '345' => [
                        'def' => ['id' => '345', 'data' => 'def'],
                        'ghi' => ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
                'data',
                'id',
            ],
            [
                [
                    '123' => [
                        'abc' => ['id' => '123', 'data' => 'abc'],
                    ],
                    '345' => [
                        'def' => ['id' => '345', 'data' => 'def'],
                        'ghi' => ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
                static fn(array $element) => $element['data'],
                'id',
            ],
            [
                [
                    '123' => [
                        'abc' => [
                            'abc' => ['id' => '123', 'data' => 'abc'],
                        ],
                    ],
                    '345' => [
                        'def' => [
                            'def' => ['id' => '345', 'data' => 'def'],
                        ],
                        'ghi' => [
                            'ghi' => ['id' => '345', 'data' => 'ghi'],
                        ],
                    ],
                ],
                'data',
                ['id', 'data'],
            ],
            [
                [
                    '123' => [
                        'abc' => [
                            'abc' => ['id' => '123', 'data' => 'abc'],
                        ],
                    ],
                    '345' => [
                        'def' => [
                            'def' => ['id' => '345', 'data' => 'def'],
                        ],
                        'ghi' => [
                            'ghi' => ['id' => '345', 'data' => 'ghi'],
                        ],
                    ],
                ],
                static fn(array $element) => $element['data'],
                ['id', 'data'],
            ],
        ];
    }

    /**
     * @dataProvider dataIndexGroupBy
     */
    public function testIndexGroupBy(array $expected, $key, $groups): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];

        $this->assertSame($expected, ArrayHelper::index($array, $key, $groups));
        $this->assertSame($expected, ArrayHelper::index(new IterableObject($array), $key, $groups));
    }

    public function testInvalidIndexInArray(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            'data',
        ];

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($array, 'id');
    }

    public function testInvalidIndexInIterable(): void
    {
        $iterableObject = new IterableObject([
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            'data',
        ]);

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($iterableObject, 'id');
    }

    public function testInvalidIndexWithoutKeyInArray(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            'data',
        ];

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($array, null);
    }

    public function testInvalidIndexWithoutKeyInIterable(): void
    {
        $iterableObject = new IterableObject([
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            'data',
        ]);

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($iterableObject, null);
    }

    public function testInvalidIndexGroupByInArray(): void
    {
        $array = ['id' => '1'];

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($array, null, ['id']);
    }

    public function testInvalidIndexGroupByInIterable(): void
    {
        $iterableObject = new IterableObject(['id' => '1']);

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($iterableObject, null, ['id']);
    }

    public function testInvalidIndexGroupByWithKeyInArray(): void
    {
        $array = ['id' => '1'];

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($array, 'id', ['id']);
    }

    public function testInvalidIndexGroupByWithKeyInIterable(): void
    {
        $iterableObject = new IterableObject(['id' => '1']);

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::index($iterableObject, 'id', ['id']);
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/11739
     */
    public function testIndexFloat(): void
    {
        $array = [
            ['id' => 1e6],
            ['id' => 1e32],
            ['id' => 1e64],
            ['id' => 1465540807.522109],
        ];

        $expected = [
            '1000000' => ['id' => 1e6],
            '1.0E+32' => ['id' => 1e32],
            '1.0E+64' => ['id' => 1e64],
            '1465540807.5221' => ['id' => 1465540807.522109],
        ];

        $this->assertSame($expected, ArrayHelper::index($array, 'id'));
        $this->assertSame($expected, ArrayHelper::index(new IterableObject($array), 'id'));
    }
}
