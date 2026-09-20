<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

use function ini_get;
use function ini_set;

final class HtmlEncodeTest extends TestCase
{
    public function testBase(): void
    {
        $array = [
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ],
            'invalid' => "a\x80b",
            'quotes \'"' => '\'"',
        ];

        $expected = [
            'abc' => '123',
            '<' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ],
            'invalid' => 'a�b',
            'quotes \'"' => '&#039;&quot;',
        ];

        $this->assertSame($expected, ArrayHelper::htmlEncode($array));
        $this->assertSame($expected, ArrayHelper::htmlEncode(new IterableObject($array)));
    }

    public function testValuesOnly(): void
    {
        $array = [
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ],
            'invalid' => "a\x80b",
            'quotes \'"' => '\'"',
        ];

        $expected = [
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '&lt;&gt;' => 'a&lt;&gt;b',
                '23' => true,
            ],
            'invalid' => 'a�b',
            'quotes &#039;&quot;' => '&#039;&quot;',
        ];

        $this->assertEquals($expected, ArrayHelper::htmlEncode($array, false));
        $this->assertEquals($expected, ArrayHelper::htmlEncode(new IterableObject($array), false));
    }

    public function testExplicitEncodingIsUsed(): void
    {
        $this->assertSame(["\xE9"], ArrayHelper::htmlEncode(["\xE9"], true, 'ISO-8859-1'));
    }

    public function testDefaultEncodingUsesDefaultCharset(): void
    {
        $previous = ini_get('default_charset');
        ini_set('default_charset', 'ISO-8859-1');

        try {
            $this->assertSame(["\xE9"], ArrayHelper::htmlEncode(["\xE9"]));
        } finally {
            ini_set('default_charset', $previous);
        }
    }
}
