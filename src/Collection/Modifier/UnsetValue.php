<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class UnsetValue implements DataModifierInterface
{
    private $key = null;

    public function apply(array $data): array
    {
        if ($this->key !== null) {
            unset($data[$this->key]);
        }
        return $data;
    }

    public function forKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }
}
