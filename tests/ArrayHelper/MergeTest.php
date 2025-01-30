<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class MergeTest extends TestCase
{
    public function testEmptyMerge(): void
    {
        $this->assertEquals([], ArrayHelper::merge(...[]));
    }

    public function testMerge(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];
        $c = [
            'version' => '2.0',
            'options' => [
                'namespace' => true,
            ],
            'features' => [
                'debug',
            ],
            'foo',
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'name' => 'Yii',
            'version' => '2.0',
            'options' => [
                'namespace' => true,
                'unittest' => true,
            ],
            'features' => [
                'mvc',
                'gii',
                'debug',
            ],
            'foo',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithNullValues(): void
    {
        $a = [
            'firstValue',
            null,
        ];
        $b = [
            'secondValue',
            'thirdValue',
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'firstValue',
            null,
            'secondValue',
            'thirdValue',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeIntegerKeyedArraysWithSameValue(): void
    {
        $a = ['2019-01-25'];
        $b = ['2019-01-25'];
        $c = ['2019-01-25'];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = ['2019-01-25'];

        $this->assertEquals($expected, $result);
    }

    public static function dataParametrizedMerge(): array
    {
        return [
            'unlimited' => [
                [
                    'top' => 3,
                    'n1' => [
                        'n1-1' => 3,
                        'n1-2' => 'x',
                        'n1-3' => 'y',
                    ],
                    'n2' => [
                        'n2-1' => [1, 2, 3, 4],
                        'n2-2' => 'a',
                        'more' => [
                            'more1' => [6, 7, 8, 9],
                            'more2' => 'b',
                        ],
                    ],
                ],
                null,
            ],
            'without-recursion' => [
                [
                    'top' => 3,
                    'n1' => [
                        'n1-1' => 3,
                        'n1-3' => 'y',
                    ],
                    'n2' => [
                        'n2-1' => [3, 4],
                        'more' => [
                            'more1' => [8, 9],
                            'more2' => 'b',
                        ],
                    ],
                ],
                0,
            ],
            'level1' => [
                [
                    'top' => 3,
                    'n1' => [
                        'n1-1' => 3,
                        'n1-2' => 'x',
                        'n1-3' => 'y',
                    ],
                    'n2' => [
                        'n2-1' => [3, 4],
                        'n2-2' => 'a',
                        'more' => [
                            'more1' => [8, 9],
                            'more2' => 'b',
                        ],
                    ],
                ],
                1,
            ],
            'level2' => [
                [
                    'top' => 3,
                    'n1' => [
                        'n1-1' => 3,
                        'n1-2' => 'x',
                        'n1-3' => 'y',
                    ],
                    'n2' => [
                        'n2-1' => [1, 2, 3, 4],
                        'n2-2' => 'a',
                        'more' => [
                            'more1' => [8, 9],
                            'more2' => 'b',
                        ],
                    ],
                ],
                2,
            ],
            'level3' => [
                [
                    'top' => 3,
                    'n1' => [
                        'n1-1' => 3,
                        'n1-2' => 'x',
                        'n1-3' => 'y',
                    ],
                    'n2' => [
                        'n2-1' => [1, 2, 3, 4],
                        'n2-2' => 'a',
                        'more' => [
                            'more1' => [6, 7, 8, 9],
                            'more2' => 'b',
                        ],
                    ],
                ],
                3,
            ],
        ];
    }

    #[DataProvider('dataParametrizedMerge')]
    public function testParametrizedMerge(array $expected, ?int $depth): void
    {
        $array1 = [
            'top' => 1,
            'n1' => [
                'n1-1' => 1,
            ],
            'n2' => [
                'n2-1' => [1, 2],
                'n2-2' => 'a',
                'more' => [
                    'more1' => [6, 7],
                    'more2' => 'a',
                ],
            ],
        ];
        $array2 = [
            'top' => 2,
            'n1' => [
                'n1-1' => 2,
                'n1-2' => 'x',
            ],
            'n2' => [
                'n2-1' => [3, 4],
                'more' => [
                    'more1' => [8, 9],
                    'more2' => 'b',
                ],
            ],
        ];
        $array3 = [
            'top' => 3,
            'n1' => [
                'n1-1' => 3,
                'n1-3' => 'y',
            ],
        ];

        $result = ArrayHelper::parametrizedMerge([$array1, $array2, $array3], $depth);

        $this->assertSame($expected, $result);
    }
}
