<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

final class ReplaceValueWhole implements BeforeMergeModifierInterface, AfterMergeModifierInterface
{
    /**
     * @var int|string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    private bool $setValueAfterMerge = false;

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

    public function beforeMerge(array $arrays, int $index): array
    {
        $currentArray = $arrays[$index];

        if (!array_key_exists($this->key, $currentArray)) {
            return $arrays[$index];
        }

        foreach (array_slice($arrays, $index + 1) as $array) {
            if (array_key_exists($this->key, $array)) {
                $currentArray[$this->key] = null;
                return $currentArray;
            }
        }

        foreach (array_slice($arrays, 0, $index) as $array) {
            if (array_key_exists($this->key, $array)) {
                $this->value = $currentArray[$this->key];
                $this->setValueAfterMerge = true;
                return $currentArray;
            }
        }

        return $currentArray;
    }

    public function afterMerge(array $data): array
    {
        if ($this->setValueAfterMerge) {
            $data[$this->key] = $this->value;
            $this->setValueAfterMerge = false;
        }
        return $data;
    }
}
