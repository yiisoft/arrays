<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Move element with key `$key` before key `$beforeKey`.
 */
final class MoveValueBeforeKey implements DataModifierInterface
{
    /**
     * @var int|string
     */
    private $key;

    /**
     * @var int|string
     */
    private $beforeKey;

    /**
     * @param int|string $key
     * @param int|string $beforeKey
     */
    public function __construct($key, $beforeKey)
    {
        $this->key = $key;
        $this->beforeKey = $beforeKey;
    }

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
        if (!array_key_exists($this->key, $data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $k => $v) {
            if ($k === $this->beforeKey) {
                $result[$this->key] = $data[$this->key];
            }
            if ($k !== $this->key) {
                $result[$k] = $v;
            }
        }

        return $result;
    }
}
