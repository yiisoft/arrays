<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Removes array keys from the merge result while performing {@see ArrayHelper::merge()}.
 *
 * The modifier should be specified as
 *
 * ```php
 * RemoveKeys::class => new RemoveKeys(),
 * ```
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.0',
 * ];
 *
 * $b = [
 *    'version' => '1.1',
 *    'options' => [],
 *    RemoveKeys::class => new RemoveKeys(),
 * ];
 *
 * $result = ArrayHelper::merge($a, $b);
 * ```
 *
 * Will result in:
 *
 * ```php
 * [
 *     'Yii',
 *     '1.1',
 *     [],
 * ];
 */
final class RemoveKeys implements ModifierInterface
{
    public function apply(array $data, string $key): array
    {
        return array_values($data);
    }
}
