<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

final class GroupTest extends TestCase
{
    public function testGroup(): void
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

        self::assertEquals($expected, ArrayHelper::group($array, 'id'));
        self::assertEquals($expected, ArrayHelper::group(new IterableObject($array), 'id'));
    }
}
