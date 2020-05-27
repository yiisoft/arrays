<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Reverse arrays order
 */
class MergeReverse implements ModifierInterface
{
    public function apply(array $data, string $key): array
    {
        return $data;
    }
}
