<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier\ModifierInterface;

interface ModifierInterface
{
    public function getPriority(): int;

    public function withPriority(int $priority): self;
}
