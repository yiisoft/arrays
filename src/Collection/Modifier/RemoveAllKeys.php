<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\PrioritizedModifierInterface;

/**
 * Re-indexes an array numerically, i. e. removes all information about array keys. Based on {@see array_values}.
 *
 * Simple usage:
 *
 * ```php
 * $removeKeys = new RemoveAllKeys();
 *
 * // [1, 2]
 * $result = $removeKeys->apply(['a' => 1, 'b' => 2]);
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
 *     new RemoveAllKeys()
 * );
 *
 * // [
 * //     'Yii',
 * //     '3.0',
 * //     [],
 * // ],
 * $result = ArrayHelper::merge($a, $b);
 * ```
 */
final class RemoveAllKeys extends Modifier implements DataModifierInterface
{
    protected int $priority = Modifier::PRIORITY_LOW;

    public function apply(array $data): array
    {
        return array_values($data);
    }
}
