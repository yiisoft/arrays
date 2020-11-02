<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Removes array keys from array.
 */
final class RemoveKeys implements DataModifierInterface
{
    public function apply(array $data): array
    {
        return array_values($data);
    }
}
