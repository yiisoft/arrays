<?php

declare(strict_types=1);

namespace ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class GetValueByPathTest extends TestCase
{
    public function getValueByPathDataProvider(): array
    {
        return [
            [['key1' => ['key2' => ['key3' => 'value']]], 'key1.key2.key3', '.', 'value'],
            [['key1.key2' => ['key3' => 'value']], 'key1.key2.key3', '.', null],
            [['key1.key2' => ['key3' => 'value']], 'key1\.key2.key3', '.', 'value'],
            [['key1:key2' => ['key3' => 'value']], 'key1\:key2:key3', ':', 'value'],
        ];
    }

    /**
     * @dataProvider getValueByPathDataProvider
     */
    public function testGetValueByPath(array $array, string $path, string $delimiter, ?string $expectedValue): void
    {
        $this->assertSame($expectedValue, ArrayHelper::getValueByPath($array, $path, null, $delimiter));
    }
}
