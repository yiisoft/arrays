<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use Yiisoft\Arrays\Tests\Objects\SimpleArrayableObject;

class ObjectWithNestedArrayableObject
{
    public int $id = 1;
    public SimpleArrayableObject $array;

    public function __construct()
    {
        $this->array = new SimpleArrayableObject();
    }
}
