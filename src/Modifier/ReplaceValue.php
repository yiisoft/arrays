<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Object that represents the replacement of array value while performing {@see ArrayHelper::merge()}.
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
 *     'validDomains' => new \Yiisoft\Arrays\Modifier\ReplaceValue([
 *         'yiiframework.com',
 *         'www.yiiframework.com',
 *     ]),
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
 *     'validDomains' => [
 *         'yiiframework.com',
 *         'www.yiiframework.com',
 *     ],
 * ]
 * ```
 */
final class ReplaceValue implements ModifierInterface
{
    /**
     * @var mixed value used as replacement.
     */
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function apply(array $data, string $key): array
    {
        $data[$key] = $this->value;

        return $data;
    }
}
