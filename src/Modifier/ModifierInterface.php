<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Modifier;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    public function apply(array $data, string $key): array;
}
