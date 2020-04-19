<?php

namespace Yiisoft\Arrays\Modifier;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    public function apply(array $data, string $key): array;
}
