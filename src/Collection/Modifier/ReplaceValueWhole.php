<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

final class ReplaceValueWhole implements BeforeMergeModifierInterface
{
    /**
     * @var int|string
     */
    private $key;

    /**
     * @param int|string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
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

    public function beforeMerge(array $array, array $allArrays): array
    {
        if (!array_key_exists($this->key, $array)) {
            return $array;
        }

        $n = 0;
        foreach ($allArrays as $arr) {
            if (array_key_exists($this->key, $arr)) {
                $n++;
            }
            if ($n > 1) {
                unset($array[$this->key]);
                return $array;
            }
        }

        return $array;
    }
}
