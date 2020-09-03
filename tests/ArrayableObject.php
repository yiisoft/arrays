<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use Yiisoft\Arrays\ArrayableInterface;

class ArrayableObject implements ArrayableInterface
{

    public function fields(): array
    {
        return [
            'a' => 1,
            'b' => 2,
        ];
    }

    public function extraFields(): array
    {
        return [];
    }

    public function toArray(array $fields = [], array $expand = [], bool $recursive = true): array
    {
        return $this->fields();
    }
}
