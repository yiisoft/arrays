<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use IteratorAggregate;
use Yiisoft\Arrays\ArrayAccessTrait;

final class IterableObject implements IteratorAggregate
{
    use ArrayAccessTrait;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
