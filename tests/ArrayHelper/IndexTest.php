<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class IndexTest extends TestCase
{
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
}
