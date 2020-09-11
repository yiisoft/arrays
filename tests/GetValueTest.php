<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Arrays\ArrayHelper;

final class GetValueTest extends TestCase
{
    private array $array = [
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
                'name' => 'world',
            ],
            '1.0.status' => 'dev',
            2 => 'two'
        ],
    ];

    /**
     * @return array[] common test data for [[testGetValue()]] and [[testGetValueByPath()]]
     */
    private function commonDataProviderFromArray(): array
    {
        return [
            ['name', 'test'],
            ['noname', null],
            ['noname', 'test', 'test'],
            [
                static function ($array, $defaultValue) {
                    return $array['date'] . $defaultValue;
                },
                '31-12-2113test',
                'test',
            ],
            [['version', 2], 'two'],
            [['version', 2.0], 'two'],
        ];
    }

    public function dataProviderGetValueFromArray(): array
    {
        return array_merge($this->commonDataProviderFromArray(), [
            ['admin.firstname', 'Qiang'],
            ['admin.lastname', 'Xue'],
            [['version', '1.0', 'status'], 'released'],
            [['version', '1.0', 'date'], 'defaultValue', 'defaultValue'],
            [['version', '1.0.name'], 'defaultValue', 'defaultValue'],
            [['post', 'author.name'], 'defaultValue', 'defaultValue'],
        ]);
    }

    /**
     * @dataProvider dataProviderGetValueFromArray
     *
     * @param $key
     * @param $expected
     * @param null $default
     */
    public function testGetValueFromArray($key, $expected, $default = null): void
    {
        $this->assertEquals($expected, ArrayHelper::getValue($this->array, $key, $default));
    }

    public function dataProviderGetValueByPathFromArray(): array
    {
        return array_merge($this->commonDataProviderFromArray(), [
            ['post.id', 5],
            ['post.id', 5, 'test'],
            ['nopost.id', null],
            ['nopost.id', 'test', 'test'],
            ['post.author.name', 'cebe'],
            ['post.author.noname', null],
            ['post.author.noname', 'test', 'test'],
            ['post.author.profile.title', '1337'],
            ['admin.firstname', 'test', 'test'],
            ['admin.lastname', 'cebe'],
            ['version.1.0.status', null],
            ['post.id.value', 'defaultValue', 'defaultValue'],
            ['version.2', 'two'],
            [['version', '1.0', 'status'], null],
            [['version', '1.0'], 'defaultValue', 'defaultValue'],
            [['post', 'author.name'], 'cebe'],
            [['post', ['author', ['profile.title']]], '1337'],
        ]);
    }

    /**
     * @dataProvider dataProviderGetValueByPathFromArray
     *
     * @param $key
     * @param $expected
     * @param null $default
     */
    public function testGetValueByPathFromArray($key, $expected, $default = null): void
    {
        $this->assertEquals($expected, ArrayHelper::getValueByPath($this->array, $key, $default));
    }

    public function testGetValueByPathWithCustomDelimiter(): void
    {
        $array = [
            'post' => [
                'caption' => 'Hello, World!',
                'author' => [
                    'name' => 'Vladimir',
                ],
            ],
        ];

        $this->assertEquals('Hello, World!', ArrayHelper::getValueByPath($array, 'post~caption', null, '~'));
        $this->assertEquals('Vladimir', ArrayHelper::getValueByPath($array, ['post', 'author~name'], null, '~'));
        $this->assertNull(ArrayHelper::getValueByPath($array, 'post.caption', null, '~'));
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


        $array = [
            'a.b' => [
                'c' => 'value1',
            ],
        ];
        $this->assertEquals(null, ArrayHelper::getValueByPath($array, 'a.b.c'));
        ArrayHelper::setValueByPath($array, 'a.b.c', 'newValue');
        $this->assertEquals('newValue', ArrayHelper::getValueByPath($array, 'a.b.c'));
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

        $this->assertEquals(155, ArrayHelper::getValueByPath($object, 'subObject.id'));
    }

    public function testGetNestedValueFromObjectThatFromArrayFromObject(): void
    {
        $subObject = new stdClass();
        $subObject->id = 200;

        $object = new stdClass();
        $object->subObject = ['sub' => $subObject];

        $this->assertEquals(200, ArrayHelper::getValueByPath($object, 'subObject.sub.id'));
    }

    public function testGetNestedValueFromObjectFromArray(): void
    {
        $stdClass = new stdClass();
        $stdClass->id = 250;
        $object = ['main' => $stdClass];

        $this->assertEquals(250, ArrayHelper::getValueByPath($object, 'main.id'));
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

    public function testGetValueFromStaticProperty(): void
    {
        $object = new StaticObject();
        $this->assertSame(1, ArrayHelper::getValue($object, 'a'));
        $this->assertSame(2, ArrayHelper::getValueByPath($object, 'nested.b'));
    }

    public function testGetUndefinedPropertyFromObject(): void
    {
        $object = new stdClass();
        if (PHP_VERSION_ID >= 80000) {
            $this->expectWarning();
        } else {
            $this->expectNotice();
        }
        ArrayHelper::getValue($object, 'var');
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
        $this->assertEquals('Wilmer', ArrayHelper::getValueByPath($storage, 'magic.name'));
    }

    public function testNonExistingNestedMagicObjectProperty(): void
    {
        $order = new stdClass();
        $order->magic = new Magic([]);

        $this->expectException(\InvalidArgumentException::class);
        ArrayHelper::getValueByPath($order, 'magic.name');
    }

    public function testGetValueFromInvalidArray()
    {
        $this->expectExceptionMessage(
            'getValue() can not get value from integer. Only array and object are supported.'
        );
        ArrayHelper::getValue(42, 'key');
    }
}
