<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\ObjectWithNestedArrayableObject;
use Yiisoft\Arrays\Tests\Objects\Post1;
use Yiisoft\Arrays\Tests\Objects\Post2;
use Yiisoft\Arrays\Tests\Objects\Post3;

final class ArrayHelperTest extends TestCase
{
    public function testToArray(): void
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

    public function testRemoveValueMultiple(): void
    {
        $array = [
            'Bob' => 'Dylan',
            'Michael' => 'Jackson',
            'Mick' => 'Jagger',
            'Janet' => 'Jackson',
        ];

        $removed = ArrayHelper::removeValue($array, 'Jackson');

        $this->assertEquals(
            [
                'Bob' => 'Dylan',
                'Mick' => 'Jagger',
            ],
            $array
        );
        $this->assertEquals(
            [
                'Michael' => 'Jackson',
                'Janet' => 'Jackson',
            ],
            $removed
        );
    }

    public function testRemoveValueNotExisting(): void
    {
        $array = [
            'Bob' => 'Dylan',
            'Michael' => 'Jackson',
            'Mick' => 'Jagger',
            'Janet' => 'Jackson',
        ];

        $removed = ArrayHelper::removeValue($array, 'Marley');

        $this->assertEquals(
            [
                'Bob' => 'Dylan',
                'Michael' => 'Jackson',
                'Mick' => 'Jagger',
                'Janet' => 'Jackson',
            ],
            $array
        );
        $this->assertEquals([], $removed);
    }

    public function testIndex(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];
        $result = ArrayHelper::index($array, 'id');
        $this->assertEquals(
            [
                '123' => ['id' => '123', 'data' => 'abc'],
                '345' => ['id' => '345', 'data' => 'ghi'],
            ],
            $result
        );

        $result = ArrayHelper::index(
            $array,
            static function ($element) {
                return $element['data'];
            }
        );
        $this->assertEquals(
            [
                'abc' => ['id' => '123', 'data' => 'abc'],
                'def' => ['id' => '345', 'data' => 'def'],
                'ghi' => ['id' => '345', 'data' => 'ghi'],
            ],
            $result
        );

        $result = ArrayHelper::index($array, null);
        $this->assertEquals([], $result);

        $result = ArrayHelper::index(
            $array,
            static function () {
                return null;
            }
        );
        $this->assertEquals([], $result);

        $result = ArrayHelper::index(
            $array,
            static function ($element) {
                return $element['id'] === '345' ? null : $element['id'];
            }
        );
        $this->assertEquals(
            [
                '123' => ['id' => '123', 'data' => 'abc'],
            ],
            $result
        );
    }

    public function testIndexGroupBy(): void
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
            ['id' => '345', 'data' => 'ghi'],
        ];

        $expected = [
            '123' => [
                ['id' => '123', 'data' => 'abc'],
            ],
            '345' => [
                ['id' => '345', 'data' => 'def'],
                ['id' => '345', 'data' => 'ghi'],
            ],
        ];
        $result = ArrayHelper::index($array, null, ['id']);
        $this->assertEquals($expected, $result);
        $result = ArrayHelper::index($array, null, 'id');
        $this->assertEquals($expected, $result);

        $result = ArrayHelper::index($array, null, ['id', 'data']);
        $this->assertEquals(
            [
                '123' => [
                    'abc' => [
                        ['id' => '123', 'data' => 'abc'],
                    ],
                ],
                '345' => [
                    'def' => [
                        ['id' => '345', 'data' => 'def'],
                    ],
                    'ghi' => [
                        ['id' => '345', 'data' => 'ghi'],
                    ],
                ],
            ],
            $result
        );

        $expected = [
            '123' => [
                'abc' => ['id' => '123', 'data' => 'abc'],
            ],
            '345' => [
                'def' => ['id' => '345', 'data' => 'def'],
                'ghi' => ['id' => '345', 'data' => 'ghi'],
            ],
        ];
        $result = ArrayHelper::index($array, 'data', ['id']);
        $this->assertEquals($expected, $result);
        $result = ArrayHelper::index($array, 'data', 'id');
        $this->assertEquals($expected, $result);
        $result = ArrayHelper::index(
            $array,
            static function ($element) {
                return $element['data'];
            },
            'id'
        );
        $this->assertEquals($expected, $result);

        $expected = [
            '123' => [
                'abc' => [
                    'abc' => ['id' => '123', 'data' => 'abc'],
                ],
            ],
            '345' => [
                'def' => [
                    'def' => ['id' => '345', 'data' => 'def'],
                ],
                'ghi' => [
                    'ghi' => ['id' => '345', 'data' => 'ghi'],
                ],
            ],
        ];
        $result = ArrayHelper::index($array, 'data', ['id', 'data']);
        $this->assertEquals($expected, $result);
        $result = ArrayHelper::index(
            $array,
            static function ($element) {
                return $element['data'];
            },
            ['id', 'data']
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/11739
     */
    public function testIndexFloat(): void
    {
        $array = [
            ['id' => 1e6],
            ['id' => 1e32],
            ['id' => 1e64],
            ['id' => 1465540807.522109],
        ];

        $expected = [
            '1000000' => ['id' => 1e6],
            '1.0E+32' => ['id' => 1e32],
            '1.0E+64' => ['id' => 1e64],
            '1465540807.5221' => ['id' => 1465540807.522109],
        ];

        $result = ArrayHelper::index($array, 'id');

        $this->assertEquals($expected, $result);
    }

    public function testGetColumn(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];
        $result = ArrayHelper::getColumn($array, 'id');
        $this->assertEquals(['a' => '123', 'b' => '345'], $result);
        $result = ArrayHelper::getColumn($array, 'id', false);
        $this->assertEquals(['123', '345'], $result);

        $result = ArrayHelper::getColumn(
            $array,
            static function ($element) {
                return $element['data'];
            }
        );
        $this->assertEquals(['a' => 'abc', 'b' => 'def'], $result);
        $result = ArrayHelper::getColumn(
            $array,
            static function ($element) {
                return $element['data'];
            },
            false
        );
        $this->assertEquals(['abc', 'def'], $result);
    }

    public function testMap(): void
    {
        $array = [
            ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
            ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
            ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
        ];

        $result = ArrayHelper::map($array, 'id', 'name');
        $this->assertEquals(
            [
                '123' => 'aaa',
                '124' => 'bbb',
                '345' => 'ccc',
            ],
            $result
        );

        $result = ArrayHelper::map($array, 'id', 'name', 'class');
        $this->assertEquals(
            [
                'x' => [
                    '123' => 'aaa',
                    '124' => 'bbb',
                ],
                'y' => [
                    '345' => 'ccc',
                ],
            ],
            $result
        );
    }

    public function testKeyExists(): void
    {
        $array = [
            'a' => 1,
            'B' => 2,
        ];
        $this->assertTrue(ArrayHelper::keyExists($array, 'a'));
        $this->assertFalse(ArrayHelper::keyExists($array, 'b'));
        $this->assertTrue(ArrayHelper::keyExists($array, 'B'));
        $this->assertFalse(ArrayHelper::keyExists($array, 'c'));

        $this->assertTrue(ArrayHelper::keyExists($array, 'a', false));
        $this->assertTrue(ArrayHelper::keyExists($array, 'b', false));
        $this->assertTrue(ArrayHelper::keyExists($array, 'B', false));
        $this->assertFalse(ArrayHelper::keyExists($array, 'c', false));
    }

    public function testIsAssociative(): void
    {
        $this->assertFalse(ArrayHelper::isAssociative([]));
        $this->assertFalse(ArrayHelper::isAssociative([1, 2, 3]));
        $this->assertFalse(ArrayHelper::isAssociative([1], false));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test']));
        $this->assertFalse(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3]));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3], false));
    }

    public function testIsIndexed(): void
    {
        $this->assertTrue(ArrayHelper::isIndexed([]));
        $this->assertTrue(ArrayHelper::isIndexed([1, 2, 3]));
        $this->assertTrue(ArrayHelper::isIndexed([2 => 'a', 3 => 'b']));
        $this->assertFalse(ArrayHelper::isIndexed([2 => 'a', 3 => 'b'], true));
        $this->assertFalse(ArrayHelper::isIndexed(['a' => 'b'], false));
    }

    public function testHtmlEncode(): void
    {
        $array = [
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ],
            'invalid' => "a\x80b",
        ];
        $this->assertEquals(
            [
                'abc' => '123',
                '<' => '&gt;',
                'cde' => false,
                3 => 'blank',
                [
                    '<>' => 'a&lt;&gt;b',
                    '23' => true,
                ],
                'invalid' => 'a�b',
            ],
            ArrayHelper::htmlEncode($array)
        );
        $this->assertEquals(
            [
                'abc' => '123',
                '&lt;' => '&gt;',
                'cde' => false,
                3 => 'blank',
                [
                    '&lt;&gt;' => 'a&lt;&gt;b',
                    '23' => true,
                ],
                'invalid' => 'a�b',
            ],
            ArrayHelper::htmlEncode($array, false)
        );
    }

    public function testHtmlDecode(): void
    {
        $array = [
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ],
        ];
        $this->assertEquals(
            [
                'abc' => '123',
                '&lt;' => '>',
                'cde' => false,
                3 => 'blank',
                [
                    '<>' => 'a<>b',
                    '23' => true,
                ],
            ],
            ArrayHelper::htmlDecode($array)
        );
        $this->assertEquals(
            [
                'abc' => '123',
                '<' => '>',
                'cde' => false,
                3 => 'blank',
                [
                    '<>' => 'a<>b',
                    '23' => true,
                ],
            ],
            ArrayHelper::htmlDecode($array, false)
        );
    }

    public function testIsIn(): void
    {
        $this->assertTrue(ArrayHelper::isIn('a', new ArrayObject(['a', 'b'])));
        $this->assertTrue(ArrayHelper::isIn('a', ['a', 'b']));

        $this->assertTrue(ArrayHelper::isIn('1', new ArrayObject([1, 'b'])));
        $this->assertTrue(ArrayHelper::isIn('1', [1, 'b']));

        $this->assertFalse(ArrayHelper::isIn('1', new ArrayObject([1, 'b']), true));
        $this->assertFalse(ArrayHelper::isIn('1', [1, 'b'], true));

        $this->assertTrue(ArrayHelper::isIn(['a'], new ArrayObject([['a'], 'b'])));
        $this->assertFalse(ArrayHelper::isIn('a', new ArrayObject([['a'], 'b'])));
        $this->assertFalse(ArrayHelper::isIn('a', [['a'], 'b']));
    }

    public function testIsInStrict(): void
    {
        // strict comparison
        $this->assertTrue(ArrayHelper::isIn(1, new ArrayObject([1, 'a']), true));
        $this->assertTrue(ArrayHelper::isIn(1, [1, 'a'], true));

        $this->assertFalse(ArrayHelper::isIn('1', new ArrayObject([1, 'a']), true));
        $this->assertFalse(ArrayHelper::isIn('1', [1, 'a'], true));
    }

    public function testIsSubset(): void
    {
        $this->assertTrue(ArrayHelper::isSubset(['a'], new ArrayObject(['a', 'b'])));
        $this->assertTrue(ArrayHelper::isSubset(new ArrayObject(['a']), ['a', 'b']));

        $this->assertTrue(ArrayHelper::isSubset([1], new ArrayObject(['1', 'b'])));
        $this->assertTrue(ArrayHelper::isSubset(new ArrayObject([1]), ['1', 'b']));

        $this->assertFalse(ArrayHelper::isSubset([1], new ArrayObject(['1', 'b']), true));
        $this->assertFalse(ArrayHelper::isSubset(new ArrayObject([1]), ['1', 'b'], true));
    }

    public function testFilter(): void
    {
        $array = [
            'A' => [
                'B' => 1,
                'C' => 2,
                'D' => [
                    'E' => 1,
                    'F' => 2,
                ],
            ],
            'G' => 1,
        ];

        // Include tests
        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                ],
            ],
            ArrayHelper::filter($array, ['A.B'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A.D'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A.D.E'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['A', 'G'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                    ],
                ],
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['A.D.E', 'G'])
        );

        // Exclude (combined with include) tests
        $this->assertEquals(
            [
                'A' => [
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.B'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['!A.B', 'A'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.D.E'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.D'])
        );

        $this->assertEquals(
            [
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['G', '!X'])
        );
    }

    public function testFilterNonExistingKeys(): void
    {
        $array = [
            'A' => [
                'B' => 1,
                'C' => 2,
                'D' => [
                    'E' => 1,
                    'F' => 2,
                ],
            ],
            'G' => 1,
        ];

        $this->assertEquals([], ArrayHelper::filter($array, ['X']));
        $this->assertEquals([], ArrayHelper::filter($array, ['X.Y']));
        $this->assertEquals([], ArrayHelper::filter($array, ['A.X']));
    }

    /**
     * Values that evaluate to `true` with `empty()` tests
     */
    public function testFilterEvaluatedToEmpty(): void
    {
        $input = [
            'a' => 0,
            'b' => '',
            'c' => false,
            'd' => null,
            'e' => true,
        ];

        $this->assertEquals(ArrayHelper::filter($input, array_keys($input)), $input);
    }

    public function testGetObjectVars()
    {
        $this->assertSame([
            'id' => 123,
            'content' => 'test'
        ], ArrayHelper::getObjectVars(new Post2()));
    }
}
