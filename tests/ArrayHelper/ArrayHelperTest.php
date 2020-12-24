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

    public function testGetColumn(): void
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];
        $result = ArrayHelper::getColumn($array, 'id');
        $this->assertEquals(['a' => '123', 'b' => '345'], $result);
        $result = ArrayHelper::getColumn($array, 'id', false);
        $this->assertEquals(['123', '345'], $result);

        $result = ArrayHelper::getColumn(
            $array,
            static function ($element) {
                return $element['data'];
            }
        );
        $this->assertEquals(['a' => 'abc', 'b' => 'def'], $result);
        $result = ArrayHelper::getColumn(
            $array,
            static function ($element) {
                return $element['data'];
            },
            false
        );
        $this->assertEquals(['abc', 'def'], $result);
    }

    public function testKeyExists(): void
    {
        $array = [
            'a' => 1,
            'B' => 2,
        ];
        $this->assertTrue(ArrayHelper::keyExists($array, 'a'));
        $this->assertFalse(ArrayHelper::keyExists($array, 'b'));
        $this->assertTrue(ArrayHelper::keyExists($array, 'B'));
        $this->assertFalse(ArrayHelper::keyExists($array, 'c'));

        $this->assertTrue(ArrayHelper::keyExists($array, 'a', false));
        $this->assertTrue(ArrayHelper::keyExists($array, 'b', false));
        $this->assertTrue(ArrayHelper::keyExists($array, 'B', false));
        $this->assertFalse(ArrayHelper::keyExists($array, 'c', false));
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

    public function testFilter(): void
    {
        $array = [
            'A' => [
                'B' => 1,
                'C' => 2,
                'D' => [
                    'E' => 1,
                    'F' => 2,
                ],
            ],
            'G' => 1,
        ];

        // Include tests
        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                ],
            ],
            ArrayHelper::filter($array, ['A.B'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A.D'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A.D.E'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['A', 'G'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'D' => [
                        'E' => 1,
                    ],
                ],
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['A.D.E', 'G'])
        );

        // Exclude (combined with include) tests
        $this->assertEquals(
            [
                'A' => [
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.B'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'C' => 2,
                    'D' => [
                        'E' => 1,
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['!A.B', 'A'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                    'D' => [
                        'F' => 2,
                    ],
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.D.E'])
        );

        $this->assertEquals(
            [
                'A' => [
                    'B' => 1,
                    'C' => 2,
                ],
            ],
            ArrayHelper::filter($array, ['A', '!A.D'])
        );

        $this->assertEquals(
            [
                'G' => 1,
            ],
            ArrayHelper::filter($array, ['G', '!X'])
        );
    }

    public function testFilterNonExistingKeys(): void
    {
        $array = [
            'A' => [
                'B' => 1,
                'C' => 2,
                'D' => [
                    'E' => 1,
                    'F' => 2,
                ],
            ],
            'G' => 1,
        ];

        $this->assertEquals([], ArrayHelper::filter($array, ['X']));
        $this->assertEquals([], ArrayHelper::filter($array, ['X.Y']));
        $this->assertEquals([], ArrayHelper::filter($array, ['A.X']));
    }

    /**
     * Values that evaluate to `true` with `empty()` tests
     */
    public function testFilterEvaluatedToEmpty(): void
    {
        $input = [
            'a' => 0,
            'b' => '',
            'c' => false,
            'd' => null,
            'e' => true,
        ];

        $this->assertEquals(ArrayHelper::filter($input, array_keys($input)), $input);
    }

    public function testGetObjectVars()
    {
        $this->assertSame([
            'id' => 123,
            'content' => 'test',
        ], ArrayHelper::getObjectVars(new Post2()));
    }
}
