<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\ArrayHelper;

final class MergeWithKeysAsReverseMerge implements BeforeMergeModifierInterface, DataModifierInterface
{
    private array $array = [];

    public function beforeMerge(array $array, array $allArrays): array
    {
        $this->array = $array;
        return $array;
    }

    public function apply(array $data): array
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

            if (is_array($value) && is_array($result[$key])) {
                $result[$key] = $this->applyOrder($result[$key], $value);
            }
        }

        return array_merge($result, $data);
    }
}
