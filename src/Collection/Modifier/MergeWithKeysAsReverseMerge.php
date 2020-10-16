<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class MergeWithKeysAsReverseMerge implements ModifierInterface
{
    public function apply(array $data): array
    {
        return $data;
    }
}
