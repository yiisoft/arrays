<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayReader;

final class ArrayReaderTest extends TestCase
{
    public function testExample(): void
    {
        $reader = new ArrayReader([
            'id' => '42',
            'name' => 'Hello World!',
            'description' => null,
            'note' => '',
            'user' => [
                'first_name' => 'Mike',
                'last_name' => '',
                'age' => '19',
            ],
        ]);

        $this->assertSame(42, $reader->integer('id'));
        $this->assertSame('Hello World!', $reader->string('name'));
        $this->assertNull($reader->stringOrNull('description'));
        $this->assertNull($reader->stringOrNull('note'));
        $this->assertSame('Mike', $reader->stringOrNullByPath('user.first_name'));
        $this->assertSame(null, $reader->stringOrNullByPath('user.last_name'));
        $this->assertSame(19, $reader->integerOrNullByPath('user.age'));
    }
}
