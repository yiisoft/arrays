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
            ['x' => new ArrayCollection(['a' => 1, 'b' => 2], new RemoveKeys())],
            ['x' => new ArrayCollection(['b' => 5, 'c' => 9])]
        );

        $this->assertSame(['x' => [1, 5, 9]], $collection->toArray());
    }
}
