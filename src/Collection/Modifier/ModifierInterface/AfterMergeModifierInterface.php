<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

interface AfterMergeModifierInterface extends ModifierInterface
{
    public function afterMerge(array $data): array;
}
