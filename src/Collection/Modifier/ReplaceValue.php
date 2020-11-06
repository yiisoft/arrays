<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

/**
 * Modifier "Replace Value".
 *
 * Работает только при объединении.
 *
 * Модификатор позволяет указать элемент массива из текущей коллекции, который
 * будет особенным образом обработан при объединении.
 *
 * - Если в предыдущих массивах есть элементы с таким же ключом, то они будут заменены значением
 *   из текущего массива без объединения.
 *
 * - Если в последующих массивах есть элементы с таким же ключом, то они заменят значение из
 *   текущего массива без объединения.
 *
 * Если в текущем коллекции нет элемента массива с заданным ключом, то модификатор ничего не изменит.
 */
final class ReplaceValue implements BeforeMergeModifierInterface, AfterMergeModifierInterface
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
    public function withKey($key): self
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
