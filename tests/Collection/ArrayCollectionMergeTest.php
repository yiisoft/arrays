<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\InsertValueBeforeKey;
use Yiisoft\Arrays\Collection\Modifier\MergeWithKeysAsReverseMerge;
use Yiisoft\Arrays\Collection\Modifier\RemoveKeys;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValue;
use Yiisoft\Arrays\Collection\Modifier\ReverseValues;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionMergeTest extends TestCase
{
    public function testEmptyMerge(): void
    {
        $this->assertEquals([], (new ArrayCollection())->mergeWith(...[])->toArray());
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
        $b = (new ArrayCollection([
            'version' => '1.1',
            // 'options' => new UnsetValue(),
            'features' => [
                'gii',
            ],
        ]))->withModifier(
            (new UnsetValue())->forKey('options')
        );

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
        $b = (new ArrayCollection([
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
//            'features' => new ReplaceValue(
//                [
//                    'gii',
//                ]
//            ),
        ]))->withModifier(
            (new ReplaceValue())
                ->forKey('features')
                ->toValue(['gii'])
        );

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
        $b = (new ArrayCollection([
            'version' => '1.1',
            'options' => [],
//            new RemoveKeys(),
        ]))->withModifier(
            new RemoveKeys()
        );

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
            'meta' => ['a' => 1],
        ];
        $b = (new ArrayCollection([
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
            ],
//            ReverseBlockMerge::class => new ReverseBlockMerge(),
        ]))->withModifier(
            new MergeWithKeysAsReverseMerge(),
        );

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'version' => '1.1',
            'options' => [
                'option1' => 'valueB',
                'option2' => 'valueBB',
                'option3' => 'valueAA',
            ],
            'name' => 'Yii',
            'meta' => ['a' => 1],
        ];

        $this->assertSame($expected, $result);
    }

    public function testMergeSimpleArrayWithReverseBlock(): void
    {
        $a = [
            'A',
            'B',
        ];
        $b = (new ArrayCollection([
            'C',
            'B',
            //  ReverseBlockMerge::class => new ReverseBlockMerge(),
        ]))->withModifier(
            new MergeWithKeysAsReverseMerge()
        );

        $this->assertSame(['C', 'B', 'A'], ArrayHelper::merge($a, $b));
    }

//    public function testMergeWithAlmostReverseBlock(): void
//    {
//        $a = [
//            'name' => 'Yii',
//            'version' => '1.0',
//        ];
//        $b = [
//            'version' => '1.1',
//            ReverseBlockMerge::class => 'hello',
//        ];
//
//        $this->assertSame([
//            'name' => 'Yii',
//            'version' => '1.1',
//            ReverseBlockMerge::class => 'hello',
//        ], ArrayHelper::merge($a, $b));
//    }

    public function testMergeWithReverseValues(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = (new ArrayCollection([
            'version' => '1.1',
            'options' => [],
            //ReverseValues::class => new ReverseValues(),
        ]))->withModifier(
            new ReverseValues()
        );

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

    public function testMergeWithReverseBlockAndIntKeys(): void
    {
        $a = [
            'A',
            'B',
        ];
        $b = (new ArrayCollection([
            'C',
            'D',
            //ReverseBlockMerge::class => new ReverseBlockMerge(),
        ]))->withModifier(
            new MergeWithKeysAsReverseMerge()
        );

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'C',
            'D',
            'A',
            'B',
        ];

        $this->assertSame($expected, $result);
    }
}
