<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

/**
 * Modifiers implementing this interface are applied to the merged array after the merge is done.
 */
interface AfterMergeModifierInterface extends ModifierInterface
{
    /**
     * @param array $data Merged data to apply modifier to.
     * @return array Resulting data.
     */
    public function afterMerge(array $data): array;
}
