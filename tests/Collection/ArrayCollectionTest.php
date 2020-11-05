<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\ArrayCollectionIsImmutableException;
use Yiisoft\Arrays\Collection\Modifier\RemoveKeys;
use Yiisoft\Arrays\Collection\Modifier\ReverseValues;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionTest extends TestCase
{
    public function testImmutableSet(): void
    {
        $collection = new ArrayCollection();

        $this->expectException(ArrayCollectionIsImmutableException::class);
        $collection[7] = 42;
    }

    public function testImmutableUnset(): void
    {
        $collection = new ArrayCollection([7 => 42]);

        $this->expectException(ArrayCollectionIsImmutableException::class);
        unset($collection[7]);
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
}
