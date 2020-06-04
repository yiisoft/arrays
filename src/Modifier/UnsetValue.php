<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Modifier;

/**
 * Object that represents the removal of array value while performing {@see ArrayHelper::merge()}.
 *
 * Usage example:
 *
 * ```php
 * $array1 = [
 *     'ids' => [
 *         1,
 *     ],
 *     'validDomains' => [
 *         'example.com',
 *         'www.example.com',
 *     ],
 * ];
 *
 * $array2 = [
 *     'ids' => [
 *         2,
 *     ],
 *     'validDomains' => new \Yiisoft\Arrays\Modifier\UnsetValue(),
 * ];
 *
 * $result = \Yiisoft\Arrays\ArrayHelper::merge($array1, $array2);
 * ```
 *
 * The result will be
 *
 * ```php
 * [
 *     'ids' => [
 *         1,
 *         2,
 *     ],
 * ]
 * ```
 */
final class UnsetValue implements ModifierInterface
{
    public function apply(array $data, $key): array
    {
        unset($data[$key]);

        return $data;
    }
}
