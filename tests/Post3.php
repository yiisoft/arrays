<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

final class Post3
{
    public $id = 33;
    public $subObject;

    public function __construct()
    {
        $this->subObject = new Post2();
    }
}
