<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

final class StaticObject
{
    public static int $a = 1;
    public NestedStaticObject $nested;

    public function __construct()
    {
        $this->nested = new NestedStaticObject();
    }
}
