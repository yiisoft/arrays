<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class ReverseValues implements DataModifierInterface
{
    public function apply(array $data): array
    {
        return array_reverse($data);
    }
}
