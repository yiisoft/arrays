<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\ObjectWithNestedArrayableObject;
use Yiisoft\Arrays\Tests\Objects\ObjectWithNestedSpecificArrayableObject;
use Yiisoft\Arrays\Tests\Objects\Post1;
use Yiisoft\Arrays\Tests\Objects\Post2;
use Yiisoft\Arrays\Tests\Objects\Post3;

final class ToArrayTest extends TestCase
{
    public function testBase(): void
    {
        $data = $this->getMockBuilder(ArrayableInterface::class)
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
                ]
            )
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
                    get_class($object) => [
                        'id',
                        'secret',
                        '_content' => 'content',
                        'length' => function ($post) {
                            return strlen($post->content);
                        },
                    ],
                ]
            )
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
            ArrayHelper::toArray($object)
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
                    get_class($object) => [
                        'id',
                        'subObject',
                        'id_plus_1' => static function ($post) {
                            return $post->id + 1;
                        },
                    ],
                    get_class($object->subObject) => [
                        'id',
                        'id_plus_1' => static function ($post) {
                            return $post->id + 1;
                        },
                    ],
                ]
            )
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
                    get_class($object->subObject) => [
                        'id',
                        'id_plus_1' => static function ($post) {
                            return $post->id + 1;
                        },
                    ],
                ]
            )
        );

        $this->assertEquals(
            [
                'id' => 1,
                'array' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
            ArrayHelper::toArray(new ObjectWithNestedArrayableObject())
        );
    }

    public function dataRecursive(): array
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

    /**
     * @dataProvider dataRecursive
     *
     * @param array|object $object
     * @param bool $recursive
     * @param array $expected
     */
    public function testRecursive($object, bool $recursive, array $expected): void
    {
        $array = ArrayHelper::toArray($object, [], $recursive);
        $this->assertSame($expected, $array);
    }
}
