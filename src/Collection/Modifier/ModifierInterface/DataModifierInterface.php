<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

/**
 * Modifiers implementing this interface are applied at transformation collection to array.
 */
interface DataModifierInterface extends ModifierInterface
{
    /**
     * @param array $data Collection data.
     * @return array Resulting array.
     */
    public function apply(array $data): array;
}
