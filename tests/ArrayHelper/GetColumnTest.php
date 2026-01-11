<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

final class GetColumnTest extends TestCase
{
    public function testWithKeepKeys(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];

        $expected = ['a' => '123', 'b' => '345'];

        $this->assertSame($expected, ArrayHelper::getColumn($array, 'id'));
        $this->assertSame($expected, ArrayHelper::getColumn(new IterableObject($array), 'id'));
    }

    public function testWithoutKeepKeys(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];

        $expected = ['123', '345'];

        $this->assertSame($expected, ArrayHelper::getColumn($array, 'id', false));
        $this->assertSame($expected, ArrayHelper::getColumn(new IterableObject($array), 'id', false));
    }

    public function testClosureNameWithKeepKeys(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];

        $closure = static fn($element) => $element['data'];

        $expected = ['a' => 'abc', 'b' => 'def'];

        $this->assertSame($expected, ArrayHelper::getColumn($array, $closure));
        $this->assertSame($expected, ArrayHelper::getColumn(new IterableObject($array), $closure));
    }

    public function testClosureNameWithoutKeepKeys(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];

        $closure = static fn($element) => $element['data'];

        $expected = ['abc', 'def'];

        $this->assertSame($expected, ArrayHelper::getColumn($array, $closure, false));
        $this->assertSame($expected, ArrayHelper::getColumn(new IterableObject($array), $closure, false));
    }
}
