<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

interface BeforeMergeModifierInterface extends ModifierInterface
{
    /**
     * @param array $array
     * @param array[] $allArrays
     * @return array
     */
    public function beforeMerge(array $array, array $allArrays): array;
}
