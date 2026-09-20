<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;
use Yiisoft\Arrays\Tests\Objects\HardArrayableObject;
use Yiisoft\Arrays\Tests\Objects\SimpleArrayableObject;

class ArrayableTraitBaseTestObject implements ArrayableInterface
{
    use ArrayableTrait;

    public function fields(): array
    {
        return [];
    }
}

final class ArrayableTraitChildTestObject extends ArrayableTraitBaseTestObject
{
    public function rootFields(array $fields): array
    {
        return $this->extractRootFields($fields);
    }

    public function nestedFields(array $fields, string $root): array
    {
        return $this->extractFieldsFor($fields, $root);
    }

    public function resolvedFields(array $fields, array $expand): array
    {
        return $this->resolveFields($fields, $expand);
    }
}

final class ArrayableTraitTest extends TestCase
{
    public function testFields(): void
    {
        $object = new SimpleArrayableObject();
        $this->assertSame([
            'a' => 'a',
            'b' => 'b',
        ], $object->fields());
    }

    public function testExtraFields(): void
    {
        $object = new SimpleArrayableObject();
        $this->assertSame([], $object->extraFields());
    }

    public function testTraitFieldHelpersRemainExtensible(): void
    {
        $object = new ArrayableTraitChildTestObject();

        $this->assertSame(['item'], $object->rootFields(['item.id', 'item.name']));
        $this->assertSame(['id'], $object->nestedFields(['item.id', 'item.id'], 'item'));
        $this->assertSame([], $object->resolvedFields([], []));
    }

    public function testToArray(): void
    {
        $object = new SimpleArrayableObject();
        $this->assertSame(['a' => 1, 'b' => 2], $object->toArray());
        $this->assertSame(['a' => 1, 'b' => 2], $object->toArray(['*']));
        $this->assertSame(['b' => 2], $object->toArray(['b']));

        $object = new HardArrayableObject();
        $this->assertSame(
            [
                'x' => 1,
                'y' => 2,
                'nested' => [
                    'a' => 1,
                    'b' => 2,
                ],
                'nested2' => [
                    'X' => [
                        'a' => 1,
                        'b' => 2,
                    ],
                    'Y' => [
                        'a' => [
                            'x' => 1,
                            'y' => 2,
                        ],
                        'b' => [
                            'k' => 3,
                            'm' => 4,
                        ],
                    ],
                ],
                'specific' => [
                    '/x' => [
                        'a' => 1,
                    ],
                ],
            ],
            $object->toArray(),
        );
        $this->assertSame(
            [
                'nested' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            $object->toArray(['nested']),
        );
        $this->assertSame(
            [
                'nested' => [
                    'a' => 1,
                ],
            ],
            $object->toArray(['nested.a']),
        );
        $this->assertSame(
            [
                'z' => 3,
            ],
            $object->toArray([''], ['z']),
        );
        $this->assertSame(
            [
                'some' => [
                    'A' => 42,
                ],
            ],
            $object->toArray([''], ['some.A'], true),
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
            $object->toArray([''], ['some'], false),
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
            $object->toArray([''], ['some.A', 'some.C']),
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
            $object->toArray([''], ['some.A', 'some.C.C2']),
        );
        $this->assertSame(
            [
                'nested2' => [
                    'X' => [
                        'a' => 1,
                    ],
                ],
            ],
            $object->toArray(['nested2.X.a']),
        );
        $this->assertSame(
            [
                'nested2' => [
                    'Y' => [
                        'a' => [
                            'x' => 1,
                            'y' => 2,
                        ],
                    ],
                ],
            ],
            $object->toArray(['nested2.Y.a']),
        );
        $this->assertSame(
            [
                'z' => 3,
                'some' => [
                    'A' => 42,
                ],
            ],
            $object->toArray([''], ['z', 'some.A']),
        );
        $this->assertSame(
            [
                'specific' => [
                    '/x' => [
                        'a' => 1,
                    ],
                ],
            ],
            $object->toArray(['specific./x.a']),
        );
    }
}
