<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;

final class ArrayableWithArraysObject implements ArrayableInterface
{
    use ArrayableTrait;

    public array $a = ['x' => 1, 'y' => 2];
    public array $b = ['k' => 3, 'm' => 4];
}
