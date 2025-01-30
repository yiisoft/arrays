<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class GetValueByPathTest extends TestCase
{
    public static function getValueByPathDataProvider(): array
    {
        return [
            [['key1' => ['key2' => ['key3' => 'value']]], 'key1.key2.key3', '.', 'value'],
            [['key1.key2' => ['key3' => 'value']], 'key1.key2.key3', '.', null],
            [['key1.key2' => ['key3' => 'value']], 'key1\.key2.key3', '.', 'value'],
            [['key1:key2' => ['key3' => 'value']], 'key1\:key2:key3', ':', 'value'],
        ];
    }

    #[DataProvider('getValueByPathDataProvider')]
    public function testGetValueByPath(array $array, string $path, string $delimiter, ?string $expectedValue): void
    {
        $this->assertSame($expectedValue, ArrayHelper::getValueByPath($array, $path, null, $delimiter));
    }
}
