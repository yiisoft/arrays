<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class SetValueTest extends TestCase
{

    /**
     * Data provider for [[testSetValue()]].
     * @return array test data
     */
    public function dataProviderSetValue(): array
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
            [
                [
                    'key' => ['val'],
                ],
                null,
                'data',
                'data',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetValue
     *
     * @param array $arrayInput
     * @param string|array|null $key
     * @param mixed $value
     * @param mixed $expected
     */
    public function testSetValue(array $arrayInput, $key, $value, $expected): void
    {
        ArrayHelper::setValue($arrayInput, $key, $value);
        $this->assertEquals($expected, $arrayInput);
    }

    /**
     * Data provider for [[testSetValueByPath()]].
     * @return array test data
     */
    public function dataProviderSetValueByPath(): array
    {
        return array_merge($this->dataProviderSetValue(), [
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

    /**
     * @dataProvider dataProviderSetValue
     *
     * @param array $arrayInput
     * @param string|array|null $path
     * @param mixed $value
     * @param mixed $expected
     */
    public function testSetValueByPath(array $arrayInput, $path, $value, $expected): void
    {
        ArrayHelper::setValueByPath($arrayInput, $path, $value);
        $this->assertEquals($expected, $arrayInput);
    }
}
