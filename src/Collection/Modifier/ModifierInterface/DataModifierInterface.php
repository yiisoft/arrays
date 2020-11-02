<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

interface DataModifierInterface extends ModifierInterface
{
    public function apply(array $data): array;
}
