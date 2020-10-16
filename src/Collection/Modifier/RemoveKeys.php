<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

/**
 * Removes array keys from array.
 */
final class RemoveKeys implements ModifierInterface
{
    public function apply(array $data): array
    {
        return array_values($data);
    }
}
