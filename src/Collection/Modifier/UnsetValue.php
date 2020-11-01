<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class UnsetValue implements DataModifierInterface
{
    /**
     * @var int|string|null
     */
    private $key = null;

    public function apply(array $data): array
    {
        if ($this->key !== null) {
            unset($data[$this->key]);
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
}
