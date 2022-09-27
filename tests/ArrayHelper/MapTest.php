<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

final class MapTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            [
                [
                    '123' => 'aaa',
                    '124' => 'bbb',
                    '345' => 'ccc',
                ],
            ],
            [
                [
                    'x' => [
                        '123' => 'aaa',
                        '124' => 'bbb',
                    ],
                    'y' => [
                        '345' => 'ccc',
                    ],
                ],
                'class',
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(array $expected, ?string $group = null): void
    {
        $array = [
            ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
            ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
            ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
        ];

        $this->assertEquals(
            $expected,
            ArrayHelper::map($array, 'id', 'name', $group)
        );
        $this->assertEquals(
            $expected,
            ArrayHelper::map(new IterableObject($array), 'id', 'name', $group)
        );
    }

    public function dataWithoutGroup(): array
    {
        return [
            [
                [
                    '1' => '1',
                    '2' => '2-last',
                ],
                'from',
                'to',
            ],
            [
                [
                    'key-1' => '1',
                    'key-2' => '2-last',
                ],
                static fn (array $row) => "key-{$row['from']}",
                'to',
            ],
            [
                [
                    '1' => 'value-1',
                    '2' => 'value-2-last',
                ],
                'from',
                static fn (array $row) => "value-{$row['to']}",
            ],
        ];
    }

    /**
     * @dataProvider dataWithoutGroup
     */
    public function testWithoutGroup(array $expected, $from, $to): void
    {
        $array = [
            ['from' => '1', 'to' => '1'],
            ['from' => '2', 'to' => '2'],
            ['from' => '2', 'to' => '2-last'],
        ];

        $this->assertSame($expected, ArrayHelper::map($array, $from, $to));
        $this->assertSame($expected, ArrayHelper::map(new IterableObject($array), $from, $to));
    }

    public function dataWithGroup(): array
    {
        return [
            [
                [
                    '1' => [
                        '1' => '1.1',
                        '2' => '1.2',
                    ],
                    '2' => [
                        '1' => '2.1',
                        '2' => '2.2-last',
                    ],
                ],
                'from',
                'to',
                'group',
            ],
            [
                [
                    '1' => [
                        'key-1' => '1.1',
                        'key-2' => '1.2',
                    ],
                    '2' => [
                        'key-1' => '2.1',
                        'key-2' => '2.2-last',
                    ],
                ],
                static fn (array $row) => "key-{$row['from']}",
                'to',
                'group',
            ],
            [
                [
                    '1' => [
                        '1' => 'value-1.1',
                        '2' => 'value-1.2',
                    ],
                    '2' => [
                        '1' => 'value-2.1',
                        '2' => 'value-2.2-last',
                    ],
                ],
                'from',
                static fn (array $row) => "value-{$row['to']}",
                'group',
            ],
            [
                [
                    'group-1' => [
                        '1' => '1.1',
                        '2' => '1.2',
                    ],
                    'group-2' => [
                        '1' => '2.1',
                        '2' => '2.2-last',
                    ],
                ],
                'from',
                'to',
                static fn (array $row) => "group-{$row['group']}",
            ],
        ];
    }

    /**
     * @dataProvider dataWithGroup
     */
    public function testWithGroup(array $expected, $from, $to, $group): void
    {
        $array = [
            ['group' => '1', 'from' => '1', 'to' => '1.1'],
            ['group' => '1', 'from' => '2', 'to' => '1.2'],
            ['group' => '2', 'from' => '1', 'to' => '2.1'],
            ['group' => '2', 'from' => '2', 'to' => '2.2'],
            ['group' => '2', 'from' => '2', 'to' => '2.2-last'],
        ];

        $this->assertSame($expected, ArrayHelper::map($array, $from, $to, $group));
        $this->assertSame($expected, ArrayHelper::map(new IterableObject($array), $from, $to, $group));
    }
}
