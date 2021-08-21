<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

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
}
