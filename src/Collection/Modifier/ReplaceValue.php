<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class ReplaceValue implements ModifierInterface
{
    private $key = null;
    private $value = null;

    public function apply(array $data): array
    {
        if ($this->key !== null) {
            $data[$this->key] = $this->value;
        }
        return $data;
    }

    public function forKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }

    public function toValue($value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }
}
