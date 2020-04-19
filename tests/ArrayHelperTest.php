<?php

namespace Yiisoft\Arrays\Tests;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Modifier\ReplaceValue;
use Yiisoft\Arrays\Modifier\UnsetValue;

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
    }

    public function testRemove(): void
    {
        $array = ['name' => 'b', 'age' => 3];
        $name = ArrayHelper::remove($array, 'name');

        $this->assertEquals('b', $name);
        $this->assertEquals(['age' => 3], $array);

        $default = ArrayHelper::remove($array, 'nonExisting', 'defaultValue');
        $this->assertEquals('defaultValue', $default);
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

    public function testMultisort(): void
    {
        // empty key
        $dataEmpty = [];
        ArrayHelper::multisort($dataEmpty, '');
        $this->assertEquals([], $dataEmpty);

        // single key
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArrayHelper::multisort($array, 'name');
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArrayHelper::multisort($array, ['name', 'age']);
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 2], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[2]);

        // case-insensitive
        $array = [
            ['name' => 'a', 'age' => 3],
            ['name' => 'b', 'age' => 2],
            ['name' => 'B', 'age' => 4],
            ['name' => 'A', 'age' => 1],
        ];

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[1]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[2]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[3]);

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING | SORT_FLAG_CASE, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[2]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[3]);
    }

    public function testMultisortNestedObjects(): void
    {
        $obj1 = new stdClass();
        $obj1->type = 'def';
        $obj1->owner = $obj1;

        $obj2 = new stdClass();
        $obj2->type = 'abc';
        $obj2->owner = $obj2;

        $obj3 = new stdClass();
        $obj3->type = 'abc';
        $obj3->owner = $obj3;

        $models = [
            $obj1,
            $obj2,
            $obj3,
        ];

        $this->assertEquals($obj2, $obj3);

        ArrayHelper::multisort($models, 'type', SORT_ASC);
        $this->assertEquals($obj2, $models[0]);
        $this->assertEquals($obj3, $models[1]);
        $this->assertEquals($obj1, $models[2]);

        ArrayHelper::multisort($models, 'type', SORT_DESC);
        $this->assertEquals($obj1, $models[0]);
        $this->assertEquals($obj2, $models[1]);
        $this->assertEquals($obj3, $models[2]);
    }

    public function testMultisortUseSort(): void
    {
        // single key
        $orders = [
            'name' => SORT_ASC,
        ];

        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArrayHelper::multisort($array, array_keys($orders), array_values($orders));
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $orders = [
            'name' => SORT_ASC,
            'age' => SORT_DESC,
        ];

        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArrayHelper::multisort($array, array_keys($orders), array_values($orders));
        $this->assertEquals(['name' => 'a', 'age' => 2], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[2]);
    }

    public function testMultisortClosure(): void
    {
        $changelog = [
            '- Enh #123: test1',
            '- Bug #125: test2',
            '- Bug #123: test2',
            '- Enh: test3',
            '- Bug: test4',
        ];
        $i = 0;
        ArrayHelper::multisort(
            $changelog,
            static function ($line) use (&$i) {
                if (preg_match('/^- (Enh|Bug)( #\d+)?: .+$/', $line, $m)) {
                    $o = ['Bug' => 'C', 'Enh' => 'D'];
                    return $o[$m[1]] . ' ' . (!empty($m[2]) ? $m[2] : 'AAAA' . $i++);
                }

                return 'B' . $i++;
            },
            SORT_ASC,
            SORT_NATURAL
        );
        $this->assertEquals(
            [
                '- Bug #123: test2',
                '- Bug #125: test2',
                '- Bug: test4',
                '- Enh #123: test1',
                '- Enh: test3',
            ],
            $changelog
        );
    }

    public function testMultisortInvalidArgumentExceptionDirection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArrayHelper::multisort($data, ['foo'], []);
    }

    public function testMultisortInvalidArgumentExceptionSortFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArrayHelper::multisort($data, ['foo'], ['foo'], []);
    }

    public function testEmptyMerge(): void
    {
        $this->assertEquals([], ArrayHelper::merge(...[]));
    }

    public function testMerge(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];
        $c = [
            'version' => '2.0',
            'options' => [
                'namespace' => true,
            ],
            'features' => [
                'debug',
            ],
            'foo',
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'name' => 'Yii',
            'version' => '2.0',
            'options' => [
                'namespace' => true,
                'unittest' => true,
            ],
            'features' => [
                'mvc',
                'gii',
                'debug',
            ],
            'foo',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithUnset(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => new UnsetValue(),
            'features' => [
                'gii',
            ],
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'features' => [
                'mvc',
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithReplace(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => new ReplaceValue(
                [
                    'gii',
                ]
            ),
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'namespace' => false,
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testMergeWithNullValues(): void
    {
        $a = [
            'firstValue',
            null,
        ];
        $b = [
            'secondValue',
            'thirdValue'
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'firstValue',
            null,
            'secondValue',
            'thirdValue',
        ];

        $this->assertEquals($expected, $result);
    }


    public function testMergeIntegerKeyedArraysWithSameValue(): void
    {
        $a = ['2019-01-25'];
        $b = ['2019-01-25'];
        $c = ['2019-01-25'];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = ['2019-01-25'];

        $this->assertEquals($expected, $result);
    }

    /**
     * @see https://github.com/yiisoft/yii2/pull/11549
     */
    public function testFloatKey(): void
    {
        $array = [];
        $array[1.0] = 'some value';

        $result = ArrayHelper::getValue($array, 1.0);

        $this->assertEquals('some value', $result);
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

    public function getValueFromArrayProvider(): array
    {
        return [
            ['name', 'test'],
            ['noname', null],
            ['noname', 'test', 'test'],
            ['post.id', 5],
            ['post.id', 5, 'test'],
            ['nopost.id', null],
            ['nopost.id', 'test', 'test'],
            ['post.author.name', 'cebe'],
            ['post.author.noname', null],
            ['post.author.noname', 'test', 'test'],
            ['post.author.profile.title', '1337'],
            ['admin.firstname', 'Qiang'],
            ['admin.firstname', 'Qiang', 'test'],
            ['admin.lastname', 'Xue'],
            [
                static function ($array, $defaultValue) {
                    return $array['date'] . $defaultValue;
                },
                '31-12-2113test',
                'test',
            ],
            [['version', '1.0', 'status'], 'released'],
            [['version', '1.0', 'date'], 'defaultValue', 'defaultValue'],
        ];
    }

    /**
     * @dataProvider getValueFromArrayProvider
     *
     * @param $key
     * @param $expected
     * @param null $default
     */
    public function testGetValueFromArray($key, $expected, $default = null): void
    {
        $array = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'cebe',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin.firstname' => 'Qiang',
            'admin.lastname' => 'Xue',
            'admin' => [
                'lastname' => 'cebe',
            ],
            'version' => [
                '1.0' => [
                    'status' => 'released',
                ],
            ],
        ];

        $this->assertEquals($expected, ArrayHelper::getValue($array, $key, $default));
    }

    /**
     * @see https://github.com/yiisoft/arrays/issues/1
     */
    public function testGetValueConsistentWithSetValue(): void
    {
        $array = [
            'a.b' => [
                'c' => 'value1',
            ],
        ];
        $this->assertEquals(null, ArrayHelper::getValue($array, 'a.b.c'));
        ArrayHelper::setValue($array, 'a.b.c', 'newValue');
        $this->assertEquals('newValue', ArrayHelper::getValue($array, 'a.b.c'));
    }

    public function testGetValueObjects(): void
    {
        $arrayObject = new ArrayObject(['id' => 23], ArrayObject::ARRAY_AS_PROPS);
        $this->assertEquals(23, ArrayHelper::getValue($arrayObject, 'id'));

        $object = new Post1();
        $this->assertEquals(23, ArrayHelper::getValue($object, 'id'));
    }

    public function testGetNestedObjectsValueFromObject(): void
    {
        $object = new stdClass();
        $object->subObject = new stdClass();
        $object->subObject->id = 155;

        $this->assertEquals(155, ArrayHelper::getValue($object, 'subObject.id'));
    }

    public function testGetNestedValueFromObjectThatFromArrayFromObject(): void
    {
        $subObject = new stdClass();
        $subObject->id = 200;

        $object = new stdClass();
        $object->subObject = ['sub' => $subObject];

        $this->assertEquals(200, ArrayHelper::getValue($object, 'subObject.sub.id'));
    }

    public function testGetNestedValueFromObjectFromArray(): void
    {
        $stdClass = new stdClass();
        $stdClass->id = 250;
        $object = ['main' => $stdClass];

        $this->assertEquals(250, ArrayHelper::getValue($object, 'main.id'));
    }

    /**
     * This is expected to result in a PHP error.
     */
    public function testGetValueNonexistingProperties1(): void
    {
        $this->expectError();
        $object = new Post1();
        $this->assertNull(ArrayHelper::getValue($object, 'nonExisting'));
    }

    /**
     * This is expected to result in a PHP error.
     */
    public function testGetValueNonexistingProperties2(): void
    {
        $this->expectError();
        $arrayObject = new ArrayObject(['id' => 23], ArrayObject::ARRAY_AS_PROPS);
        $this->assertEquals(23, ArrayHelper::getValue($arrayObject, 'nonExisting'));
    }

    /**
     * Data provider for [[testSetValue()]].
     * @return array test data
     */
    public function dataProviderSetValue(): array
    {
        return [
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key',
                'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                    'key' => 'val',
                ],
            ],
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key2',
                'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val',
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in',
                'val',
                [
                    'key1' => 'val1',
                    'key' => ['in' => 'val'],
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key.in',
                'val',
                [
                    'key' => [
                        'val1',
                        'in' => 'val',
                    ],
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key',
                ['in' => 'val'],
                [
                    'key' => ['in' => 'val'],
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in.0',
                'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => ['val'],
                    ],
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr',
                'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr',
                ['val'],
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => ['val1'],
                    ],
                ],
                'key.in.arr',
                'val',
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],

            [
                [
                    'key' => ['in' => 'val1'],
                ],
                'key.in.arr',
                ['val'],
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in.0',
                ['arr' => 'val'],
                [
                    'key' => [
                        'in' => [
                            ['arr' => 'val'],
                            'key' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in',
                ['arr' => 'val'],
                [
                    'key' => [
                        'in' => ['arr' => 'val'],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
                'key.in.schema',
                'array',
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'schema' => 'array',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                        ],
                    ],
                ],
                ['key', 'in.array', 'ok.schema'],
                'array',
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                            'ok.schema' => 'array',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => ['val'],
                ],
                null,
                'data',
                'data',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSetValue
     *
     * @param array $arrayInput
     * @param string|array|null $key
     * @param mixed $value
     * @param mixed $expected
     */
    public function testSetValue(array $arrayInput, $key, $value, $expected): void
    {
        ArrayHelper::setValue($arrayInput, $key, $value);
        $this->assertEquals($expected, $arrayInput);
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

    public function testIsArray(): void
    {
        $this->assertTrue(ArrayHelper::isTraversable(['a']));
        $this->assertTrue(ArrayHelper::isTraversable(new ArrayObject(['1'])));
        $this->assertFalse(ArrayHelper::isTraversable(new stdClass()));
        $this->assertFalse(ArrayHelper::isTraversable('A,B,C'));
        $this->assertFalse(ArrayHelper::isTraversable(12));
        $this->assertFalse(ArrayHelper::isTraversable(false));
        $this->assertFalse(ArrayHelper::isTraversable(null));
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

    public function testExistingMagicObjectProperty(): void
    {
        $magic = new Magic(['name' => 'Wilmer']);
        $this->assertEquals('Wilmer', ArrayHelper::getValue($magic, 'name'));
    }

    public function testNonExistingMagicObjectProperty(): void
    {
        $magic = new Magic([]);

        $this->expectException(\InvalidArgumentException::class);

        ArrayHelper::getValue($magic, 'name');
    }

    public function testExistingNestedMagicObjectProperty(): void
    {
        $storage = new stdClass();
        $storage->magic = new Magic(['name' => 'Wilmer']);
        $this->assertEquals('Wilmer', ArrayHelper::getValue($storage, 'magic.name'));
    }

    public function testNonExistingNestedMagicObjectProperty(): void
    {
        $order = new stdClass();
        $order->magic = new Magic([]);

        $this->expectException(\InvalidArgumentException::class);
        ArrayHelper::getValue($order, 'magic.name');
    }
}
