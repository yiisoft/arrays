<?php

declare(strict_types=1);

namespace ArrayHelper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class ParsePathTest extends TestCase
{
    public function parsePathDataProvider(): array
    {
        return [
            ['key1.key2.key3', '.', false, ['key1', 'key2', 'key3']],
            ['key1..key2..key3', '.', false, ['key1', '', 'key2', '', 'key3']],
            ['key1...key2...key3', '.', false, ['key1', '', '', 'key2', '', '', 'key3']],
            ['key1\..\.key2\..\.key3', '.', false, ['key1.', '.key2.', '.key3']],

            ['key1\.key2.key3', '.', false, ['key1.key2', 'key3']],
            ['key1\.key2.key3', '.', true, ['key1\.key2', 'key3']],
            ['key1\:key2:key3', ':', false, ['key1:key2', 'key3']],
            ['\.key1.key2', '.', false, ['.key1', 'key2']],
            ['key1.key2\.', '.', false, ['key1', 'key2.']],

            //            ['key1\\key2\\key3', '\\', false, ['key1', 'key2', 'key3']],
            //            ['key1\\\\key2\\\\key3', '\\', false, ['key1\\key2\\key3']],
            //            ['key1\\\\\\key2\\\\\\key3', '\\', false, ['key1\\\\key2\\\\key3']],
            //            ['key1\\\\\\\\key2\\\\\\\\key3', '\\', false, ['key1\\\\key2\\\\key3']],
            //            ['key1\\\\key2\\key3', '\\', false, ['key1\\key2', 'key3']],
        ];
    }

    /**
     * @dataProvider parsePathDataProvider
     */
    public function testParsePath(string $path, string $delimiter, bool $escapeDelimiter, array $expectedPath): void
    {
        $this->assertSame($expectedPath, ArrayHelper::parsePath($path, $delimiter, $escapeDelimiter));
    }

    public function testParsePathWithDelimiterAtBeginning(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Delimiter can\'t be at the very beginning.');
        ArrayHelper::parsePath('.key1.key2');
    }

    public function testParsePathWithDelimiterAtEnd(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Delimiter can\'t be at the very end.');
        ArrayHelper::parsePath('key1.key2.');
    }

    public function testParsePathWithInvalidDelimiter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only 1 character is allowed for delimiter.');

        ArrayHelper::parsePath('key1..key2.key3', '..');
    }
}
