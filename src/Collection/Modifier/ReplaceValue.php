<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class ReplaceValue implements DataModifierInterface
{
    /**
     * @var int|string|null
     */
    private $key = null;

    /**
     * @var mixed
     */
    private $value = null;

    public function apply(array $data): array
    {
        if ($this->key !== null) {
            $data[$this->key] = $this->value;
        }
        return $data;
    }

    /**
     * @param int|string $key
     * @return self
     */
    public function forKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function toValue($value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }
}
