<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\Magic;
use Yiisoft\Arrays\Tests\Objects\Post1;
use Yiisoft\Arrays\Tests\Objects\StaticObject;

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
            2 => 'two',
        ],
        '42.7' => 500,
    ];

    /**
     * @return array[] common test data for [[testGetValue()]] and [[testGetValueByPath()]]
     */
    private static function commonDataProviderFromArray(): array
    {
        return [
            ['name', 'test'],
            ['noname', null],
            ['noname', 'test', 'test'],
            [
                static fn ($array, $defaultValue) => $array['date'] . $defaultValue,
                '31-12-2113test',
                'test',
            ],
            [['version', 2], 'two'],
            [['version', 2.0], 'two'],
            [42.7, 500],
        ];
    }

    public static function dataProviderGetValueFromArray(): array
    {
        return array_merge(self::commonDataProviderFromArray(), [
            ['admin.firstname', 'Qiang'],
            ['admin.lastname', 'Xue'],
            [['version', '1.0', 'status'], 'released'],
            [['version', '1.0', 'date'], 'defaultValue', 'defaultValue'],
            [['version', '1.0.name'], 'defaultValue', 'defaultValue'],
            [['post', 'author.name'], 'defaultValue', 'defaultValue'],
            ['42.7', 500],
        ]);
    }

    #[DataProvider('dataProviderGetValueFromArray')]
    public function testGetValueFromArray($key, $expected, $default = null): void
    {
        $this->assertEquals($expected, ArrayHelper::getValue($this->array, $key, $default));
    }

    public static function dataProviderGetValueByPathFromArray(): array
    {
        return array_merge(self::commonDataProviderFromArray(), [
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
            ['42.7', null],
        ]);
    }

    #[DataProvider('dataProviderGetValueByPathFromArray')]
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
    public function testGetValueNonExistingProperties1(): void
    {
        $object = new Post1();

        $errorMessage = null;
        set_error_handler(
            static function (int $code, string $message) use (&$errorMessage) {
                $errorMessage = $message;
            }
        );
        ArrayHelper::getValue($object, 'nonExisting');
        restore_error_handler();

        $this->assertSame(
            'Undefined property: Yiisoft\Arrays\Tests\Objects\Post1::$nonExisting',
            $errorMessage
        );
    }

    /**
     * This is expected to result in a PHP error.
     */
    public function testGetValueNonExistingProperties2(): void
    {
        $arrayObject = new ArrayObject(['id' => 23], ArrayObject::ARRAY_AS_PROPS);

        $errorMessage = null;
        set_error_handler(
            static function (int $code, string $message) use (&$errorMessage) {
                $errorMessage = $message;
            }
        );
        ArrayHelper::getValue($arrayObject, 'nonExisting');
        restore_error_handler();

        $this->assertSame('Undefined array key "nonExisting"', $errorMessage);
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

        $errorMessage = null;
        set_error_handler(
            static function (int $code, string $message) use (&$errorMessage) {
                $errorMessage = $message;
            }
        );
        ArrayHelper::getValue($object, 'var');
        restore_error_handler();

        $this->assertSame('Undefined property: stdClass::$var', $errorMessage);
    }

    public function testExistingMagicObjectProperty(): void
    {
        $magic = new Magic(['name' => 'Wilmer']);
        $this->assertEquals('Wilmer', ArrayHelper::getValue($magic, 'name'));
    }

    public function testNonExistingMagicObjectProperty(): void
    {
        $magic = new Magic([]);

        $this->expectException(InvalidArgumentException::class);

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

        $this->expectException(InvalidArgumentException::class);
        ArrayHelper::getValueByPath($order, 'magic.name');
    }

    public function testDefaultArrayValue(): void
    {
        $array = [
            'a' => 1,
        ];
        $key = ['a', 'b', 'c'];
        $default = [
            'c' => 'value',
        ];
        $result = ArrayHelper::getValue($array, $key, $default);

        $this->assertSame(['c' => 'value'], $result);
    }

    public function testGetters(): void
    {
        $object = new class () {
            public function getValue(): int
            {
                return 7;
            }
        };

        $this->assertSame(7, ArrayHelper::getValue($object, 'getValue()'));
    }
}
