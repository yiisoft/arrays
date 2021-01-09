<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\ModifierInterface;

abstract class Modifier implements ModifierInterface
{
    public const PRIORITY_HIGH = 10000;
    public const PRIORITY_NORMAL = 0;
    public const PRIORITY_LOW = -10000;

    protected int $priority = self::PRIORITY_NORMAL;

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function withPriority(int $priority): self
    {
        $new = clone $this;
        $new->priority = $priority;
        return $new;
    }
}
