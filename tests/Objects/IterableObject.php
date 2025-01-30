<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use ArrayIterator;
use IteratorAggregate;

final class IterableObject implements IteratorAggregate
{
    public function __construct(
        private readonly array $data,
    ) {
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }
}
