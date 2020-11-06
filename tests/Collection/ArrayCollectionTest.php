<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\RemoveKeys;
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

    public function testWithModifier(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifier = $collection
            ->withAddedModifier(new UnsetValue());

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifier->getModifiers();

        $this->assertInstanceOf(UnsetValue::class, $modifiers[0]);
    }

    public function testWithModifiers(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifiers = $collection
            ->withModifiers([new UnsetValue()])
            ->withModifiers([new ReverseValues(), new RemoveKeys()]);

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifiers->getModifiers();

        $this->assertCount(2, $modifiers);
        $this->assertInstanceOf(ReverseValues::class, $modifiers[0]);
        $this->assertInstanceOf(RemoveKeys::class, $modifiers[1]);
    }

    public function testWithAddedModifier(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifiers = $collection
            ->withAddedModifier(new UnsetValue())
            ->withAddedModifier(new ReverseValues(), new RemoveKeys());

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifiers->getModifiers();

        $this->assertInstanceOf(UnsetValue::class, $modifiers[0]);
        $this->assertInstanceOf(ReverseValues::class, $modifiers[1]);
        $this->assertInstanceOf(RemoveKeys::class, $modifiers[2]);
    }

    public function testWithAddedModifiers(): void
    {
        $collection = new ArrayCollection();

        $collectionWithModifiers = $collection
            ->withAddedModifiers([new UnsetValue()])
            ->withAddedModifiers([new ReverseValues(), new RemoveKeys()]);

        // Test immutable
        $this->assertEmpty($collection->getModifiers());

        $modifiers = $collectionWithModifiers->getModifiers();

        $this->assertInstanceOf(UnsetValue::class, $modifiers[0]);
        $this->assertInstanceOf(ReverseValues::class, $modifiers[1]);
        $this->assertInstanceOf(RemoveKeys::class, $modifiers[2]);
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
                'name' => 'Donatello'
            ]),
        ]);

        $expected = [
            'simple' => 42,
            'nestedArray' => [
                'x' => 15,
                'y' => 16,
            ],
            'nestedCollection' => [
                'name' => 'Donatello'
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
            ['x' => new ArrayCollection(['b' => 2, 'c' => 3], new RemoveKeys())],
            ['x' => ['d' => 4]],
            ['x' => new ArrayCollection(['e' => 5, 'f' => 6])]
        );

        $this->assertSame(['x' => [1, 2, 3, 4, 5, 6]], $collection->toArray());
    }
}
