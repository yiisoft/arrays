<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;

final class SimpleArrayableObject implements ArrayableInterface
{
    use ArrayableTrait;

    public int $a = 1;
    public int $b = 2;
}
