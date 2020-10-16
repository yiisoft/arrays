<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

final class ObjectWithNestedSpecificArrayableObject
{
    public int $id = 1;
    public SpecificArrayableObject $array;

    public function __construct()
    {
        $this->array = new SpecificArrayableObject();
    }
}
