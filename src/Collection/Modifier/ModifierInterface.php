<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

/**
 * Interface ModifierInterface
 */
interface ModifierInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function apply(array $data): array;
}
