<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\ArrayCollectionIsImmutableException;
use Yiisoft\Arrays\Collection\Modifier\RemoveKeys;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class ArrayCollectionArrayAccessTest extends TestCase
{
    public function testIterator()
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new RemoveKeys()
        );
        $iterator = $collection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame([1, 2, 3], $iterator->getArrayCopy());
    }

    public function testCount()
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertSame(2, $collection->count());
    }

    public function testOffsetExists()
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertTrue($collection->offsetExists('a'));
        $this->assertFalse($collection->offsetExists('c'));
    }

    public function testOffsetGet()
    {
        $collection = new ArrayCollection(
            ['a' => 1, 'b' => 2, 'c' => 3],
            new UnsetValue('c')
        );

        $this->assertSame(1, $collection->offsetGet('a'));
        $this->assertNull($collection->offsetGet('c'));
        $this->assertNull($collection->offsetGet('d'));
    }

    public function testOffsetSet()
    {
        $collection = new ArrayCollection();

        $this->expectException(ArrayCollectionIsImmutableException::class);
        $collection->offsetSet(7, 42); // equal $collection[7] = 42;
    }

    public function testOffsetUnset()
    {
        $collection = new ArrayCollection([7 => 42]);

        $this->expectException(ArrayCollectionIsImmutableException::class);
        $collection->offsetUnset(7); // equal unset($collection[7]);
    }
}
