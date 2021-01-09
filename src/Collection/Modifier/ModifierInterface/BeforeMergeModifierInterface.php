<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

/**
 * Modifiers implementing this interface are applied before merging is done.
 */
interface BeforeMergeModifierInterface extends ModifierInterface
{
    /**
     * @param array[] $arrays Data arrays to me merged.
     * @param int $index Index of the array originally containing the modifier.
     *
     * @return array Array to use during merge.
     */
    public function beforeMerge(array $arrays, int $index): array;
}
