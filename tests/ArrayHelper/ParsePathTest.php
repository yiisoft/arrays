<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class ParsePathTest extends TestCase
{
    public function parsePathDataProvider(): array
    {
        return [
            ['key1.key2.key3', '.', '\\', false, ['key1', 'key2', 'key3']],
            ['key1..key2..key3', '.', '\\', false, ['key1', '', 'key2', '', 'key3']],
            ['key1...key2...key3', '.', '\\', false, ['key1', '', '', 'key2', '', '', 'key3']],
            ['key1\.key2.key3', '.', '\\', false, ['key1.key2', 'key3']],
            ['\.key1.key2', '.', '\\', false, ['.key1', 'key2']],
            ['key1.key2\.', '.', '\\', false, ['key1', 'key2.']],
            ['key1\..\.key2\..\.key3', '.', '\\', false, ['key1.', '.key2.', '.key3']],
            ['key1\\\.', '.', '\\', false, ['key1\.']],

            ['key1\:key2:key3', ':', '\\', false, ['key1:key2', 'key3']],

            ['key1\.key2.key3', '.', '\\', true, ['key1\.key2', 'key3']],

            ['key1\\key2\\key3', '\\', '/', false, ['key1', 'key2', 'key3']],
            ['key1\\\\key2\\\\key3', '\\', '/', false, ['key1', '', 'key2', '', 'key3']],
            ['key1\\\\\\key2\\\\\\key3', '\\', '/', false, ['key1', '', '', 'key2', '', '', 'key3']],
            ['key1/\\\\/\key2/\\\\/\key3', '\\', '/', false, ['key1\\', '\\key2\\', '\\key3']],

            ['key1\.', '.', '\\', false, ['key1.']],
            ['key1~.', '.', '~', false, ['key1.']],
            ['key1~~.', '.', '~', false, ['key1~.']],
        ];
    }

    /**
     * @dataProvider parsePathDataProvider
     */
    public function testParsePath(
        string $path,
        string $delimiter,
        string $escapeCharacter,
        bool $preserveDelimiterEscaping,
        array $expectedPath
    ): void {
        $actualPath = ArrayHelper::parsePath($path, $delimiter, $escapeCharacter, $preserveDelimiterEscaping);
        $this->assertSame($expectedPath, $actualPath);
    }

    public function testParsePathWithLongDelimiter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only 1 character is allowed for delimiter.');

        ArrayHelper::parsePath('key1..key2.key3', '..');
    }

    public function testParsePathWithLongEscapeCharacter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only 1 escape character is allowed.');

        ArrayHelper::parsePath('key1.key2.key3', '.', '//');
    }

    public function testParsePathWithDelimiterEqualsEscapeCharacter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Delimiter and escape character must be different.');

        ArrayHelper::parsePath('key1.key2.key3', '.', '.');
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
}
