<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\ObjectWithNestedArrayableObject;
use Yiisoft\Arrays\Tests\Objects\ObjectWithNestedSpecificArrayableObject;
use Yiisoft\Arrays\Tests\Objects\Post1;
use Yiisoft\Arrays\Tests\Objects\Post2;
use Yiisoft\Arrays\Tests\Objects\Post3;

use function strlen;

final class ToArrayTest extends TestCase
{
    public function testBase(): void
    {
        $data = $this
            ->getMockBuilder(ArrayableInterface::class)
            ->getMock()
            ->method('toArray')
            ->willReturn([]);

        $this->assertEquals([], ArrayHelper::toArray($data));
        $this->assertEquals(['foo'], ArrayHelper::toArray('foo'));
        $object = new Post1();
        $this->assertEquals(get_object_vars($object), ArrayHelper::toArray($object));
        $object = new Post2();
        $this->assertEquals(get_object_vars($object), ArrayHelper::toArray($object));

        $object1 = new Post1();
        $object2 = new Post2();
        $this->assertEquals(
            [
                get_object_vars($object1),
                get_object_vars($object2),
            ],
            ArrayHelper::toArray(
                [
                    $object1,
                    $object2,
                ],
            ),
        );

        $object = new Post2();
        $this->assertEquals(
            [
                'id' => 123,
                'secret' => 's',
                '_content' => 'test',
                'length' => 4,
            ],
            ArrayHelper::toArray(
                $object,
                [
                    $object::class => [
                        'id',
                        'secret',
                        '_content' => 'content',
                        'length' => fn(Post2 $post) => strlen($post->content),
                    ],
                ],
            ),
        );

        $object = new Post3();
        $this->assertEquals(get_object_vars($object), ArrayHelper::toArray($object, [], false));
        $this->assertEquals(
            [
                'id' => 33,
                'subObject' => [
                    'id' => 123,
                    'content' => 'test',
                ],
            ],
            ArrayHelper::toArray($object),
        );

        //recursive with attributes of object and subobject
        $this->assertEquals(
            [
                'id' => 33,
                'id_plus_1' => 34,
                'subObject' => [
                    'id' => 123,
                    'id_plus_1' => 124,
                ],
            ],
            ArrayHelper::toArray(
                $object,
                [
                    $object::class => [
                        'id',
                        'subObject',
                        'id_plus_1' => static fn($post) => $post->id + 1,
                    ],
                    $object->subObject::class => [
                        'id',
                        'id_plus_1' => static fn($post) => $post->id + 1,
                    ],
                ],
            ),
        );

        //recursive with attributes of subobject only
        $this->assertEquals(
            [
                'id' => 33,
                'subObject' => [
                    'id' => 123,
                    'id_plus_1' => 124,
                ],
            ],
            ArrayHelper::toArray(
                $object,
                [
                    $object->subObject::class => [
                        'id',
                        'id_plus_1' => static fn($post) => $post->id + 1,
                    ],
                ],
            ),
        );

        $this->assertEquals(
            [
                'id' => 1,
                'array' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            ArrayHelper::toArray(new ObjectWithNestedArrayableObject()),
        );
    }

    public static function dataRecursive(): array
    {
        $objectWithNestedSpecificArrayableObject = new ObjectWithNestedSpecificArrayableObject();

        return [
            [
                $objectWithNestedSpecificArrayableObject,
                false,
                [
                    'id' => 1,
                    'array' => $objectWithNestedSpecificArrayableObject->array,
                ],
            ],
            [
                $objectWithNestedSpecificArrayableObject,
                true,
                [
                    'id' => 1,
                    'array' => ['a' => 1, 'b' => 2],
                ],
            ],
            [
                ['x' => $objectWithNestedSpecificArrayableObject],
                false,
                ['x' => $objectWithNestedSpecificArrayableObject],
            ],
            [
                ['x' => $objectWithNestedSpecificArrayableObject],
                true,
                [
                    'x' => [
                        'id' => 1,
                        'array' => ['a' => 1, 'b' => 2],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('dataRecursive')]
    public function testRecursive(array|object $object, bool $recursive, array $expected): void
    {
        $array = ArrayHelper::toArray($object, [], $recursive);
        $this->assertSame($expected, $array);
    }
}
