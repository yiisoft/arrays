<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Reverse order of an array elements. Based on {@see array_reverse}.
 *
 * Simple usage:
 *
 * ```php
 * $reverse = new ReverseValues('code');
 *
 * // ['b' => 2, 'a' => 1]
 * $result = $reverse->apply(['a' => 1, 'b' => 2]);
 * ```
 *
 * Usage with merge:
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.0',
 * ];
 * $b = new ArrayCollection(
 *     [
 *         'version' => '3.0',
 *         'options' => [],
 *     ],
 *     new ReverseValues()
 * );
 *
 * // [
 * //     'options' => [],
 * //     'version' => '3.0',
 * //     'name' => 'Yii',
 * // ],
 * $result = ArrayHelper::merge($a, $b);
 * ```
 */
final class ReverseValues implements DataModifierInterface
{
    public function apply(array $data): array
    {
        return array_reverse($data);
    }
}
