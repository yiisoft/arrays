<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

use InvalidArgumentException;

use function array_key_exists;

final class Magic
{
    public function __construct(private array $attributes) {}

    public function __get(string $attribute)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            throw new InvalidArgumentException("There is no \"$attribute\"");
        }

        return $this->attributes[$attribute];
    }

    public function __set(string $attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __isset(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }
}
