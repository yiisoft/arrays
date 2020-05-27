<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Reverse arrays order
 */
class ReverseBlockMerge implements ModifierInterface
{
    public function apply(array $data, string $key): array
    {
        return $data;
    }
}
