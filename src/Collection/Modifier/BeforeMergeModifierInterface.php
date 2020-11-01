<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

interface BeforeMergeModifierInterface extends ModifierInterface
{
    public function beforeMerge(array $array, array $allArrays): array;
}
