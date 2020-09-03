<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Modifier\InsertValueBeforeKey;
use Yiisoft\Arrays\Modifier\RemoveKeys;
use Yiisoft\Arrays\Modifier\ReplaceValue;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Arrays\Modifier\ReverseValues;
use Yiisoft\Arrays\Modifier\UnsetValue;

final class ArrayMergeTest extends TestCase
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

    public function testMergeWithUnset(): void
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
            'options' => new UnsetValue(),
            'features' => [
                'gii',
            ],
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'features' => [
                'mvc',
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithReplace(): void
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
            'features' => new ReplaceValue(
                [
                    'gii',
                ]
            ),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'namespace' => false,
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithRemoveKeys(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            new RemoveKeys(),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'Yii',
            '1.1',
            [],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithReverseBlock(): void
    {
        $a = [
            'name' => 'Yii',
            'options' => [
                'option1' => 'valueA',
                'option3' => 'valueAA',
            ],
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
            ],
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
                'option3' => 'valueAA',
            ],
            'name' => 'Yii',
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithReverseValues(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            ReverseValues::class => new ReverseValues(),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'options' => [],
            'version' => '1.1',
            'name' => 'Yii',
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithNullValues(): void
    {
        $a = [
            'firstValue',
            null,
        ];
        $b = [
            'secondValue',
            'thirdValue'
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

    public function testMergeWithInsertValueBeforekey(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = [
            'version' => '1.1',
            'options' => [],
            'vendor' => new InsertValueBeforeKey('Yiisoft', 'name'),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'vendor' => 'Yiisoft',
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [],
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithEndReverseBlocks(): void
    {
        $a = [
            'A' => 1,
            'B' => 2,
        ];
        $b = [
            'C' => 3,
            'D' => 4,
        ];
        $c = [
            'X' => 5,
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'X' => 5,
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
        ];

        $this->assertSame($expected, $result);
    }

    public function t1estMergeWithMiddleReverseBlockAndStringKeys(): void
    {
        $a = [
            'A' => 1,
            'B' => 2,
        ];
        $b = [
            'C' => 3,
            'D' => 4,
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];
        $c = [
            'X' => 5,
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'C' => 3,
            'D' => 4,
            'A' => 1,
            'B' => 2,
            'X' => 5,
        ];

        $this->assertSame($expected, $result);
    }

    public function t1estMergeWithMiddleReverseBlockAndIntKeys(): void
    {
        $a = [
            'A',
            'B',
        ];
        $b = [
            'C',
            'D',
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];
        $c = [
            'X',
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'C',
            'D',
            'A',
            'B',
            'X',
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeWithMiddleAndEndReverseBlocks(): void
    {
        $a = [
            'A' => 1,
            'B' => 2,
        ];
        $b = [
            'C' => 3,
            'D' => 4,
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];
        $c = [
            'X' => 5,
            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'X' => 5,
            'C' => 3,
            'D' => 4,
            'A' => 1,
            'B' => 2,
        ];

        $this->assertSame($expected, $result);
    }
}
