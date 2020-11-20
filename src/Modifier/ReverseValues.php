<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Modifier;

/**
 * Class ReverseValues
 *
 * @package Yiisoft\Arrays\Modifier
 *
 * This modifier reverses array values after merge.
 *
 * It should be specified as
 *
 * ```php
 * ReverseValues::class => new ReverseValues(),
 * ```
 *
 * Usage example:
 *
 * ```php
 *
 * use Yiisoft\Arrays\Modifier\ReverseValues;
 *
 * $array1 = [
 *     'paths' => [
 *         '/tmp/tmp',
 *         ReverseValues::class => new ReverseValues(),
 *     ],
 * ];
 *
 * $array2 = [
 *     'paths' => [
 *         '/usr/bin',
 *     ],
 * ];
 *
 * $result = \Yiisoft\Arrays\ArrayHelper::merge($array1, $array2);
 * ```
 *
 * The result will be
 *
 * ```php
 * [
 *     'paths' => [
 *         '/usr/bin',
 *         '/tmp/tmp',
 *     ],
 * ]
 * ```
 */
class ReverseValues implements ModifierInterface
{
    public function apply(array $data, $key): array
    {
        return array_reverse($data);
    }
}
