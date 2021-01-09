<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\Modifier;
use Yiisoft\Arrays\Collection\Modifier\MoveValueBeforeKey;
use Yiisoft\Arrays\Collection\Modifier\RemoveAllKeys;
use Yiisoft\Arrays\Collection\Modifier\ReverseValues;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionTest extends TestCase
{
    public function testWithData(): void
    {
        $collection = new ArrayCollection();

        $collectionWithData = $collection->withData([1, 2]);

        // Test immutable
        $this->assertEmpty($collection->toArray());

        $this->assertSame([1, 2], $collectionWithData->toArray());
    }

    public function testWithModifiers(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifiers = $collection
            ->withModifiers(new UnsetValue('a'))
            ->withModifiers(new ReverseValues(), new RemoveAllKeys());

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifiers->getModifiers();

        $this->assertCount(2, $modifiers);
        $this->assertInstanceOf(ReverseValues::class, $modifiers[0]);
        $this->assertInstanceOf(RemoveAllKeys::class, $modifiers[1]);
    }

    public function testWithAddedModifiers(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifiers = $collection
            ->withAddedModifiers(new UnsetValue('a'))
            ->withAddedModifiers(new ReverseValues(), new RemoveAllKeys());

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifiers->getModifiers();

        $this->assertInstanceOf(UnsetValue::class, $modifiers[0]);
        $this->assertInstanceOf(ReverseValues::class, $modifiers[1]);
        $this->assertInstanceOf(RemoveAllKeys::class, $modifiers[2]);
    }

    public function testToArray(): void
    {
        $collection = new ArrayCollection([
            'simple' => 42,
            'nestedArray' => [
                'x' => 15,
                'y' => 16,
            ],
            'nestedCollection' => new ArrayCollection([
                'name' => 'Donatello',
            ]),
        ]);

        $expected = [
            'simple' => 42,
            'nestedArray' => [
                'x' => 15,
                'y' => 16,
            ],
            'nestedCollection' => [
                'name' => 'Donatello',
            ],
        ];

        $this->assertSame($expected, $collection->toArray());
    }

    public function testMergeWith(): void
    {
        $collection = (new ArrayCollection(['a' => 1, 'b' => 3]))
            ->mergeWith(['b' => 2, 'c' => 3]);

        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $collection->toArray());
    }

    public function testMergeWithNestedCollections(): void
    {
        $collection = (new ArrayCollection())->mergeWith(
            ['x' => ['a' => 1]],
            ['x' => new ArrayCollection(['b' => 2, 'c' => 3], new RemoveAllKeys())],
            ['x' => ['d' => 4]],
            ['x' => new ArrayCollection(['e' => 5, 'f' => 6])]
        );

        $this->assertSame(['x' => [1, 2, 3, 4, 5, 6]], $collection->toArray());
    }

    public function testEmptyMerge(): void
    {
        $this->assertEquals([], (new ArrayCollection())->mergeWith(...[])->toArray());
    }

    public function testPrioritizedModifiers(): void
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new RemoveAllKeys(),
            new MoveValueBeforeKey('a', 'c'),
            (new ReverseValues())->withPriority(Modifier::PRIORITY_HIGH),
        );

        $this->assertSame([1, 3, 2], $collection->toArray());
    }

    public function testClone(): void
    {
        $collectionA = new ArrayCollection(['a' => 1, 'b' => 2], new UnsetValue('a'));
        $this->assertSame(['b' => 2], $collectionA->toArray());

        $collectionB = $collectionA->withModifiers(new UnsetValue('b'));
        $this->assertSame(['a' => 1], $collectionB->toArray());
    }
}
