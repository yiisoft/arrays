<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Result will be ordered by source the opposite to actual merge.
 * It is especially useful for merging module config with core config
 * where more specific config has more priority.
 *
 * The modifier should be specified as
 *
 * ```php
 * ReverseBlockMerge::class => new ReverseBlockMerge(),
 * ```
 *
 * For example:
 *
 * ```php
 * $one = [
 *    'f' => 'f1',
 *    'b' => [
 *        'b1' => 'b11',
 *        'b2' => 'b33',
 *     ],
 *     'g' => 'g1',
 *     'h',
 * ];
 *
 * $two = [
 *     'a' => 'a1',
 *     'b' => [
 *         'b1' => 'bv1',
 *         'b2' => 'bv2',
 *     ],
 *     'd',
 * ];
 *
 * $three = [
 *     'a' => 'a2',
 *     'c' => 'c1',
 *     'b' => [
 *         'b2' => 'bv22',
 *         'b3' => 'bv3',
 *     ],
 *     'e',
 *     ReverseBlockMerge::class => new ReverseBlockMerge(),
 * ];
 *
 * $result = ArrayHelper::mergeReverse($one, $two, $three);
 * ```
 *
 * Will result in:
 *
 * ```php
 * [
 *     'a' => 'a2',
 *     'c' => 'c1',
 *     'b' => [
 *         'b2' => 'bv22',
 *         'b3' => 'bv3',
 *         'b1' => 'bv1',
 *     ]
 *     0 => 'e',
 *     1 => 'd',
 *     'f' => 'f1',
 *     'g' => 'g1',
 *     2 => 'h',
 * ]
 * ```
 *
 * @see ArrayHelper::performReverseBlockMerge()
 */
final class ReverseBlockMerge implements ModifierInterface
{
    public function apply(array $data, string $key): array
    {
        return $data;
    }
}
