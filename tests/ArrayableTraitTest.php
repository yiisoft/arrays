<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use PHPUnit\Framework\TestCase;

class ArrayableTraitTest extends TestCase
{

    public function testFields()
    {
        $object = new SimpleArrayableObject();
        $this->assertSame([
            'a' => 'a',
            'b' => 'b',
        ], $object->fields());
    }

    public function testExtraFields()
    {
        $object = new SimpleArrayableObject();
        $this->assertSame([], $object->extraFields());
    }

    public function testToArray()
    {
        $object = new SimpleArrayableObject();
        $this->assertSame(['a' => 1, 'b' => 2], $object->toArray());
        $this->assertSame(['a' => 1, 'b' => 2], $object->toArray(['*']));
        $this->assertSame(['b' => 2], $object->toArray(['b']));

        $object = new HardArrayableObject();
        $this->assertSame(
            [
                'nested' => [
                    'a' => 1,
                    'b' => 2
                ],
            ],
            $object->toArray(['nested'])
        );
        $this->assertSame(
            [
                'nested' => [
                    'a' => 1,
                ],
            ],
            $object->toArray(['nested.a'])
        );
        $this->assertSame(
            [
                'z' => 3
            ],
            $object->toArray([''], ['z'])
        );
        $this->assertSame(
            [
                'some' => [
                    'A' => 42,
                ],
            ],
            $object->toArray([''], ['some.A'], true)
        );
        $this->assertSame(
            [
                'some' => [
                    'A' => 42,
                    'B' => 84,
                    'C' => [
                        'C1' => 1,
                        'C2' => 2,
                    ],
                ],
            ],
            $object->toArray([''], ['some'], false)
        );
        $this->assertSame(
            [
                'some' => [
                    'A' => 42,
                    'C' => [
                        'C1' => 1,
                        'C2' => 2,
                    ],
                ],
            ],
            $object->toArray([''], ['some.A', 'some.C'])
        );
        $this->assertSame(
            [
                'some' => [
                    'A' => 42,
                    'C' => [
                        'C2' => 2,
                    ],
                ],
            ],
            $object->toArray([''], ['some.A', 'some.C.C2'])
        );
        $this->assertSame(
            [
                'nested2' => [
                    'X' => [
                        'a' => 1,
                    ],
                ],
            ],
            $object->toArray(['nested2.X.a'])
        );
    }
}
