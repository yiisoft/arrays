<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\MergeWithKeysAsReverseMerge;

final class ArrayCollectionMergeTest extends TestCase
{
    public function testEmptyMerge(): void
    {
        $this->assertEquals([], (new ArrayCollection())->mergeWith(...[])->toArray());
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
