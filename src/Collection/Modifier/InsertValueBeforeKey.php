<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Modifier "Insert Value Before Key"
 */
final class InsertValueBeforeKey implements DataModifierInterface
{
    /**
     * @var int|string|null
     */
    private $key = null;

    /**
     * @var mixed
     */
    private $value = null;

    /**
     * @var int|string|null
     */
    private $beforeKey = null;

    /**
     * @param int|string $key
     * @return self
     */
    public function withKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    /**
     * @param int|string $key
     * @return self
     */
    public function beforeKey($key): self
    {
        $new = clone $this;
        $new->beforeKey = $key;
        return $new;
    }

    public function apply(array $data): array
    {
        if ($this->key === null || $this->beforeKey === null) {
            return $data;
        }

        $result = [];
        foreach ($data as $k => $v) {
            if ($k === $this->beforeKey) {
                $result[$this->key] = $this->value;
            }
            $result[$k] = $v;
        }

        return $result;
    }
}
