<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\IterableObject;

final class HtmlDecodeTest extends TestCase
{
    public function testBase(): void
    {
        $array = [
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ],
        ];

        $expected = [
            'abc' => '123',
            '&lt;' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ],
        ];

        $this->assertSame($expected, ArrayHelper::htmlDecode($array));
        $this->assertSame($expected, ArrayHelper::htmlDecode(new IterableObject($array)));
    }

    public function testValuesOnly(): void
    {
        $array = [
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ],
        ];

        $expected = [
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ],
        ];


        $this->assertSame($expected, ArrayHelper::htmlDecode($array, false));
        $this->assertSame($expected, ArrayHelper::htmlDecode(new IterableObject($array), false));
    }
}
