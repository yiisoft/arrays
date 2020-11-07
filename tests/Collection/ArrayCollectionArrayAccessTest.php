<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\ArrayCollectionIsImmutableException;
use Yiisoft\Arrays\Collection\Modifier\RemoveAllKeys;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionArrayAccessTest extends TestCase
{
    public function testIterator(): void
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new RemoveAllKeys()
        );
        $iterator = $collection->getIterator();

        $this->assertSame([1, 2, 3], $iterator->getArrayCopy());
    }

    public function testCount(): void
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertSame(2, $collection->count());
    }

    public function testOffsetExists(): void
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertTrue($collection->offsetExists('a'));
        $this->assertFalse($collection->offsetExists('c'));
    }

    public function testOffsetGet(): void
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertSame(1, $collection->offsetGet('a'));
        $this->assertNull($collection->offsetGet('c'));
        $this->assertNull($collection->offsetGet('d'));
    }

    public function testOffsetSet(): void
    {
        $collection = new ArrayCollection();

        $this->expectException(ArrayCollectionIsImmutableException::class);
        $collection->offsetSet(7, 42); // equal $collection[7] = 42;
    }

    public function testOffsetUnset(): void
    {
        $collection = new ArrayCollection([7 => 42]);

        $this->expectException(ArrayCollectionIsImmutableException::class);
        $collection->offsetUnset(7); // equal unset($collection[7]);
    }
}
