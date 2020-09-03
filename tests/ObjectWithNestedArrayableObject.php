<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

class ObjectWithNestedArrayableObject
{
    public int $id = 1;
    public SimpleArrayableObject $array;

    public function __construct()
    {
        $this->array = new SimpleArrayableObject();
    }
}
