<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;

class HardArrayableObject implements ArrayableInterface
{
    use ArrayableTrait;

    public int $x = 1;
    public int $y = 2;
    public SimpleArrayableObject $nested;
    public array $nested2;

    public int $z = 3;
    public array $some = [
        'A' => 42,
        'B' => 84,
        'C' => [
            'C1' => 1,
            'C2' => 2,
        ],
    ];

    public int $n = 4;

    public array $specific = [
        '/x' => [
            'a' => 1,
        ],
    ];

    public function __construct()
    {
        $this->nested = new SimpleArrayableObject();
        $this->nested2 = [
            'X' => new SimpleArrayableObject(),
        ];
    }

    public function fields(): array
    {
        return ['x', 'y', 'nested', 'nested2', 'specific'];
    }

    public function extraFields(): array
    {
        return ['z', 'some'];
    }
}
