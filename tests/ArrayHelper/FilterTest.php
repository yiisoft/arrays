<?php

declare(strict_types=1);

namespace ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class FilterTest extends TestCase
{
    public function dataFilter(): array
    {
        return [
            'topArray' => [
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
                ['A'],
            ],
            'nestedNotArray' => [
                [
                    'A' => [
                        'B' => 1,
                    ],
                ],
                ['A.B'],
            ],
            'nestedlArray' => [
                [
                    'A' => [
                        'D' => [
                            'E' => 1,
                            'F' => 2,
                        ],
                    ],
                ],
                ['A.D'],
            ],
            'deepNestedNotArray' => [
                [
                    'A' => [
                        'D' => [
                            'E' => 1,
                        ],
                    ],
                ],
                ['A.D.E'],
            ],
            'severalTop' => [
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
                ['A', 'G'],
            ],
            'nestedAndTop' => [
                [
                    'A' => [
                        'D' => [
                            'E' => 1,
                        ],
                    ],
                    'G' => 1,
                ],
                ['A.D.E', 'G'],
            ],
            'topAndExcludeNested' => [
                [
                    'A' => [
                        'C' => 2,
                        'D' => [
                            'E' => 1,
                            'F' => 2,
                        ],
                    ],
                ],
                ['A', '!A.B'],
            ],
            'excludeNestedAndTop' => [
                [
                    'A' => [
                        'C' => 2,
                        'D' => [
                            'E' => 1,
                            'F' => 2,
                        ],
                    ],
                ],
                ['!A.B', 'A'],
            ],
            'topAndExcludeDeepNested' => [
                [
                    'A' => [
                        'B' => 1,
                        'C' => 2,
                        'D' => [
                            'F' => 2,
                        ],
                    ],
                ],
                ['A', '!A.D.E'],
            ],
            'topAndExcludeNestedArray' => [
                [
                    'A' => [
                        'B' => 1,
                        'C' => 2,
                    ],
                ],
                ['A', '!A.D'],
            ],
            'topAndExcludeNotExists' => [
                [
                    'G' => 1,
                ],
                ['G', '!X'],
            ],
            'nonExistsTop' => [
                [],
                ['X'],
            ],
            'nonExistsWithNested' => [
                [],
                ['X.Y'],
            ],
            'nonExistsNested' => [
                [],
                ['A.X'],
            ],
            'nonExistsInNotArray' => [
                [],
                ['A.B.X'],
            ],
            'excludeNonExistsInNotArray' => [
                [
                    'A' => [
                        'D' => [
                            'E' => 1,
                            'F' => 2,
                        ],
                    ],
                ],
                ['A.D', '!A.D.E.X'],
            ],
        ];
    }

    /**
     * @dataProvider dataFilter
     *
     * @param array $expects
     * @param array $filter
     */
    public function testFilter(array $expects, array $filter): void
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
        $this->assertSame($expects, ArrayHelper::filter($array, $filter));
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

        $this->assertSame(
            $input,
            ArrayHelper::filter($input, array_keys($input))
        );
    }
}
