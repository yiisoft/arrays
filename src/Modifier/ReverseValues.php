<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Reverses array values.
 */
class ReverseValues implements ModifierInterface
{
    public function apply(array $data, string $key): array
    {
        return array_reverse($data);
    }
}
