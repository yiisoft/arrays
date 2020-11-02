<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

interface BeforeMergeModifierInterface extends ModifierInterface
{
    /**
     * @param array[] $arrays
     * @param int $index
     * @return array
     */
    public function beforeMerge(array $arrays, int $index): array;
}
