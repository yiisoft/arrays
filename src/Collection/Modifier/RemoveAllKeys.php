<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Re-indexes an array numerically, i. e. removes all information about array keys.
 */
final class RemoveAllKeys implements DataModifierInterface
{
    public function apply(array $data): array
    {
        return array_values($data);
    }
}
