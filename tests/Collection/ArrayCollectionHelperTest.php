<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\ArrayCollectionHelper;
use Yiisoft\Arrays\Collection\Modifier\InsertValueBeforeKey;
use Yiisoft\Arrays\Collection\Modifier\MergeWithKeysAsReverseMerge;
use Yiisoft\Arrays\Collection\Modifier\RemoveKeys;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValue;
use Yiisoft\Arrays\Collection\Modifier\ReverseValues;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionHelperTest extends TestCase
{
    public function testEmptyMerge(): void
    {
        $this->assertEquals([], ArrayCollectionHelper::merge(...[])->toArray());
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

        $result = ArrayCollectionHelper::merge($a, $b, $c)->toArray();
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
        $b = (new ArrayCollection([
            'version' => '1.1',
            // 'options' => new UnsetValue(),
            'features' => [
                'gii',
            ],
        ]))->addModifier(
            (new UnsetValue())->forKey('options')
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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
        ]))->addModifier(
            (new ReplaceValue())
                ->forKey('features')
                ->toValue(['gii'])
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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
        ]))->addModifier(
            new RemoveKeys()
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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
        ]))->addModifier(
            new MergeWithKeysAsReverseMerge(),
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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
        ]))->addModifier(
            new MergeWithKeysAsReverseMerge()
        );

        $this->assertSame(['C', 'B', 'A'], ArrayCollectionHelper::merge($a, $b)->toArray());
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
        ]))->addModifier(
            new ReverseValues()
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
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

        $result = ArrayCollectionHelper::merge($a, $b, $c)->toArray();
        $expected = ['2019-01-25'];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithInsertValueBeforekey(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = (new ArrayCollection([
            'version' => '1.1',
            'options' => [],
//            'vendor' => new InsertValueBeforeKey('Yiisoft', 'name'),
        ]))->addModifier(
            (new InsertValueBeforeKey())
            ->setValue('Yiisoft')
            ->withKey('vendor')
            ->beforeKey('name')
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
        $expected = [
            'vendor' => 'Yiisoft',
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [],
        ];

        $this->assertSame($expected, $result);
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
        ]))->addModifier(
            new MergeWithKeysAsReverseMerge()
        );

        $result = ArrayCollectionHelper::merge($a, $b)->toArray();
        $expected = [
            'C',
            'D',
            'A',
            'B',
        ];

        $this->assertSame($expected, $result);
    }
}
