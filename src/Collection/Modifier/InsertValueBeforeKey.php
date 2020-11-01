<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class InsertValueBeforeKey implements DataModifierInterface
{
    private $key;

    private $value;

    private $beforeKey;

    public function withKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }

    public function setValue($value): self
    {
        $new = clone $this;
        $new->value = $value;
        return $new;
    }

    public function beforeKey($key): self
    {
        $new = clone $this;
        $new->beforeKey = $key;
        return $new;
    }


    public function apply(array $data): array
    {
        $res = [];
        foreach ($data as $k => $v) {
            if ($k === $this->beforeKey) {
                $res[$this->key] = $this->value;
            }
            $res[$k] = $v;
        }

        return $res;
    }
}
