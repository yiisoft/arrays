<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

/**
 * Modifier "Save Order"
 *
 * Модификатор запоминает порядок элементов в текущей коллекции и пытается его сохранить
 * при объединении массивов.
 */
final class SaveOrder implements BeforeMergeModifierInterface, AfterMergeModifierInterface
{
    private array $array = [];

    private bool $nested = false;

    public function nested(): self
    {
        $new = clone $this;
        $new->nested = true;
        return $new;
    }

    public function notNested(): self
    {
        $new = clone $this;
        $new->nested = false;
        return $new;
    }

    public function beforeMerge(array $arrays, int $index): array
    {
        $this->array = $arrays[$index];
        return $this->array;
    }

    public function afterMerge(array $data): array
    {
        return $this->applyOrder($data, $this->array);
    }

    private function applyOrder(array $data, array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_string($key)) {
                if (array_key_exists($key, $data)) {
                    $result[$key] = ArrayHelper::remove($data, $key);
                }
            } else {
                foreach ($data as $dataKey => $dataValue) {
                    if (is_int($dataKey) && $dataValue === $value) {
                        $result[] = $dataValue;
                        unset($data[$dataKey]);
                        break;
                    }
                }
            }

            if ($this->nested && is_array($value) && is_array($result[$key])) {
                $result[$key] = $this->applyOrder($result[$key], $value);
            }
        }

        return array_merge($result, $data);
    }
}
