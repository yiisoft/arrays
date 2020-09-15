<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Tests\Objects\ArrayAccessObject;

class ArrayAccessTraitTest extends TestCase
{
    public function testIterator()
    {
        $object = new ArrayAccessObject();
        $iterator = $object->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame($object->data, $iterator->getArrayCopy());
    }

    public function testCount()
    {
        $object = new ArrayAccessObject();
        $this->assertSame(3, $object->count());
    }

    public function testOffsetExists()
    {
        $object = new ArrayAccessObject();
        $this->assertTrue($object->offsetExists('a'));
        $this->assertFalse($object->offsetExists('x'));
    }

    public function testOffsetGet()
    {
        $object = new ArrayAccessObject();
        $this->assertSame(1, $object->offsetGet('a'));
        $this->assertNull($object->offsetGet('x'));
    }

    public function testOffsetSet()
    {
        $object = new ArrayAccessObject();
        $object->offsetSet('a', 4);
        $object->offsetSet('x', 5);
        $this->assertSame([
            'a' => 4,
            'b' => 2,
            'c' => 3,
            'x' => 5,
        ], $object->data);
    }

    public function testOffsetUnset()
    {
        $object = new ArrayAccessObject();
        $object->offsetUnset('b');
        $this->assertSame([
            'a' => 1,
            'c' => 3,
        ], $object->data);
    }
}
