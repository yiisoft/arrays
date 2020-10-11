<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;

final class SpecificArrayableObject implements ArrayableInterface
{
    use ArrayableTrait;

    public int $a = 1;
    public int $b = 2;
    public int $c = 3;

    public function fields(): array
    {
        return ['a', 'b'];
    }
}
