<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Tests\Objects\Post2;

final class ArrayHelperTest extends TestCase
{
    public function testRemoveValueMultiple(): void
    {
        $array = [
            'Bob' => 'Dylan',
            'Michael' => 'Jackson',
            'Mick' => 'Jagger',
            'Janet' => 'Jackson',
        ];

        $removed = ArrayHelper::removeValue($array, 'Jackson');

        $this->assertEquals(
            [
                'Bob' => 'Dylan',
                'Mick' => 'Jagger',
            ],
            $array
        );
        $this->assertEquals(
            [
                'Michael' => 'Jackson',
                'Janet' => 'Jackson',
            ],
            $removed
        );
    }

    public function testRemoveValueNotExisting(): void
    {
        $array = [
            'Bob' => 'Dylan',
            'Michael' => 'Jackson',
            'Mick' => 'Jagger',
            'Janet' => 'Jackson',
        ];

        $removed = ArrayHelper::removeValue($array, 'Marley');

        $this->assertEquals(
            [
                'Bob' => 'Dylan',
                'Michael' => 'Jackson',
                'Mick' => 'Jagger',
                'Janet' => 'Jackson',
            ],
            $array
        );
        $this->assertEquals([], $removed);
    }

    public function testIsAssociative(): void
    {
        $this->assertFalse(ArrayHelper::isAssociative([]));
        $this->assertFalse(ArrayHelper::isAssociative([1, 2, 3]));
        $this->assertFalse(ArrayHelper::isAssociative([1], false));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test']));
        $this->assertFalse(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3]));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3], false));
    }

    public function testIsIndexed(): void
    {
        $this->assertTrue(ArrayHelper::isIndexed([]));
        $this->assertTrue(ArrayHelper::isIndexed([1, 2, 3]));
        $this->assertTrue(ArrayHelper::isIndexed([2 => 'a', 3 => 'b']));
        $this->assertFalse(ArrayHelper::isIndexed([2 => 'a', 3 => 'b'], true));
        $this->assertTrue(ArrayHelper::isIndexed([0 => 'a', 1 => 'b'], true));
        $this->assertFalse(ArrayHelper::isIndexed(['a' => 'b'], false));
    }

    public function testHtmlEncode(): void
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
        $this->assertEquals(
            [
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
            ],
            ArrayHelper::htmlEncode($array)
        );
        $this->assertEquals(
            [
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
            ],
            ArrayHelper::htmlEncode($array, false)
        );
    }

    public function testHtmlDecode(): void
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
        $this->assertEquals(
            [
                'abc' => '123',
                '&lt;' => '>',
                'cde' => false,
                3 => 'blank',
                [
                    '<>' => 'a<>b',
                    '23' => true,
                ],
            ],
            ArrayHelper::htmlDecode($array)
        );
        $this->assertEquals(
            [
                'abc' => '123',
                '<' => '>',
                'cde' => false,
                3 => 'blank',
                [
                    '<>' => 'a<>b',
                    '23' => true,
                ],
            ],
            ArrayHelper::htmlDecode($array, false)
        );
    }

    public function testIsIn(): void
    {
        $this->assertTrue(ArrayHelper::isIn('a', new ArrayObject(['a', 'b'])));
        $this->assertTrue(ArrayHelper::isIn('a', ['a', 'b']));

        $this->assertTrue(ArrayHelper::isIn('1', new ArrayObject([1, 'b'])));
        $this->assertTrue(ArrayHelper::isIn('1', [1, 'b']));

        $this->assertFalse(ArrayHelper::isIn('1', new ArrayObject([1, 'b']), true));
        $this->assertFalse(ArrayHelper::isIn('1', [1, 'b'], true));

        $this->assertTrue(ArrayHelper::isIn(['a'], new ArrayObject([['a'], 'b'])));
        $this->assertFalse(ArrayHelper::isIn('a', new ArrayObject([['a'], 'b'])));
        $this->assertFalse(ArrayHelper::isIn('a', [['a'], 'b']));
    }

    public function testIsInStrict(): void
    {
        // strict comparison
        $this->assertTrue(ArrayHelper::isIn(1, new ArrayObject([1, 'a']), true));
        $this->assertTrue(ArrayHelper::isIn(1, [1, 'a'], true));

        $this->assertFalse(ArrayHelper::isIn('1', new ArrayObject([1, 'a']), true));
        $this->assertFalse(ArrayHelper::isIn('1', [1, 'a'], true));
    }

    public function testIsSubset(): void
    {
        $this->assertTrue(ArrayHelper::isSubset(['a'], new ArrayObject(['a', 'b'])));
        $this->assertTrue(ArrayHelper::isSubset(new ArrayObject(['a']), ['a', 'b']));

        $this->assertTrue(ArrayHelper::isSubset([1], new ArrayObject(['1', 'b'])));
        $this->assertTrue(ArrayHelper::isSubset(new ArrayObject([1]), ['1', 'b']));

        $this->assertFalse(ArrayHelper::isSubset([1], new ArrayObject(['1', 'b']), true));
        $this->assertFalse(ArrayHelper::isSubset(new ArrayObject([1]), ['1', 'b'], true));
    }

    public function testGetObjectVars()
    {
        $this->assertSame([
            'id' => 123,
            'content' => 'test',
        ], ArrayHelper::getObjectVars(new Post2()));
    }
}
