<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Yiisoft\Arrays\ArrayAccessTrait;

class ArrayAccessObject implements IteratorAggregate, ArrayAccess, Countable
{
    use ArrayAccessTrait;

    public array $data = [
        'a' => 1,
        'b' => 2,
        'c' => 3,
    ];
}
