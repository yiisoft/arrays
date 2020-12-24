<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

/**
 * @see MergeTest
 */
final class MapTest extends TestCase
{
    public function testWithoutGroup(): void
    {
        $array = [
            ['from' => '1', 'to' => '1'],
            ['from' => '2', 'to' => '2'],
            ['from' => '2', 'to' => '2-last'],
        ];

        $this->assertSame(
            [
                '1' => '1',
                '2' => '2-last',
            ],
            ArrayHelper::map($array, 'from', 'to')
        );

        $this->assertSame(
            [
                'key-1' => '1',
                'key-2' => '2-last',
            ],
            ArrayHelper::map(
                $array,
                function ($row) {
                    return "key-{$row['from']}";
                },
                'to'
            )
        );

        $this->assertSame(
            [
                '1' => 'value-1',
                '2' => 'value-2-last',
            ],
            ArrayHelper::map(
                $array,
                'from',
                function ($row) {
                    return "value-{$row['to']}";
                }
            )
        );
    }

    public function testWithGroup(): void
    {
        $array = [
            ['group' => '1', 'from' => '1', 'to' => '1.1'],
            ['group' => '1', 'from' => '2', 'to' => '1.2'],
            ['group' => '2', 'from' => '1', 'to' => '2.1'],
            ['group' => '2', 'from' => '2', 'to' => '2.2'],
            ['group' => '2', 'from' => '2', 'to' => '2.2-last'],
        ];

        $this->assertSame(
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
            ArrayHelper::map($array, 'from', 'to', 'group')
        );

        $this->assertSame(
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
            ArrayHelper::map(
                $array,
                function ($row) {
                    return "key-{$row['from']}";
                },
                'to',
                'group'
            )
        );

        $this->assertSame(
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
            ArrayHelper::map(
                $array,
                'from',
                function ($row) {
                    return "value-{$row['to']}";
                },
                'group'
            )
        );

        $this->assertSame(
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
            ArrayHelper::map(
                $array,
                'from',
                'to',
                function ($row) {
                    return "group-{$row['group']}";
                }
            )
        );
    }
}
