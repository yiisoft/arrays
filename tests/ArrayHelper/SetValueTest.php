<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class SetValueTest extends TestCase
{
    /**
     * @return array[] common test data for [[testSetValue()]] and [[testSetValueByPath()]]
     */
    private static function commonDataProvider(): array
    {
        return [
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key',
                'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                    'key' => 'val',
                ],
            ],
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key2',
                'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val',
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key',
                ['in' => 'val'],
                [
                    'key' => ['in' => 'val'],
                ],
            ],
            [
                [
                    'key' => ['val'],
                ],
                null,
                'data',
                'data',
            ],
            [
                [1 => 'a'],
                3,
                'c',
                [1 => 'a', 3 => 'c'],
            ],
            [
                [1 => 'a'],
                3.0,
                'c',
                [1 => 'a', 3 => 'c'],
            ],
            [
                [1 => 'a'],
                3.01,
                'c',
                [1 => 'a', '3.01' => 'c'],
            ],
        ];
    }

    /**
     * Data provider for [[testSetValue()]].
     *
     * @return array test data
     */
    public static function dataProviderSetValue(): array
    {
        return array_merge(self::commonDataProvider(), [
            [
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                        ],
                    ],
                ],
                ['key', 'in.array', 'ok.schema'],
                'array',
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                            'ok.schema' => 'array',
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[DataProvider('dataProviderSetValue')]
    public function testSetValue(
        array $arrayInput,
        array|float|int|string|null $key,
        mixed $value,
        mixed $expected
    ): void {
        ArrayHelper::setValue($arrayInput, $key, $value);
        $this->assertEquals($expected, $arrayInput);
    }

    /**
     * Data provider for [[testSetValueByPath()]].
     *
     * @return array test data
     */
    public static function dataProviderSetValueByPath(): array
    {
        return array_merge(self::commonDataProvider(), [
            [
                [
                    'key1' => 'val1',
                ],
                'key.in',
                'val',
                [
                    'key1' => 'val1',
                    'key' => ['in' => 'val'],
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key.in',
                'val',
                [
                    'key' => [
                        'val1',
                        'in' => 'val',
                    ],
                ],
            ],
            [
                [
                    'key1' => 'val1',
                ],
                'key.in.0',
                'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => ['val'],
                    ],
                ],
            ],
            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr',
                'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr',
                ['val'],
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => ['val1'],
                    ],
                ],
                'key.in.arr',
                'val',
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => ['in' => 'val1'],
                ],
                'key.in.arr',
                ['val'],
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in.0',
                ['arr' => 'val'],
                [
                    'key' => [
                        'in' => [
                            ['arr' => 'val'],
                            'key' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in',
                ['arr' => 'val'],
                [
                    'key' => [
                        'in' => ['arr' => 'val'],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
                'key.in.schema',
                'array',
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'schema' => 'array',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[DataProvider('dataProviderSetValueByPath')]
    public function testSetValueByPath(
        array $arrayInput,
        array|float|int|string|null $path,
        mixed $value,
        mixed $expected
    ): void {
        ArrayHelper::setValueByPath($arrayInput, $path, $value);
        $this->assertEquals($expected, $arrayInput);
    }

    public static function setValueByPathWithCustomDelimiterData(): array
    {
        return [
            [
                [],
                'post~caption',
                'Hello, World!',
                [
                    'post' => [
                        'caption' => 'Hello, World!',
                    ],
                ],
            ],
            [
                [],
                ['post', 'author~name'],
                'Vladimir',
                [
                    'post' => [
                        'author' => [
                            'name' => 'Vladimir',
                        ],
                    ],
                ],
            ],
            [
                [],
                ['post', ['author', ['name~firstName']]],
                'Vladimir',
                [
                    'post' => [
                        'author' => [
                            'name' => [
                                'firstName' => 'Vladimir',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('setValueByPathWithCustomDelimiterData')]
    public function testSetValueByPathWithCustomDelimiter(
        array $arrayInput,
        array|float|int|string|null $path,
        mixed $value,
        mixed $expected
    ): void {
        ArrayHelper::setValueByPath($arrayInput, $path, $value, '~');
        $this->assertEquals($expected, $arrayInput);
    }
}
