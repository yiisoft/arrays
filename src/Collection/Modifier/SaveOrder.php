<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

use function is_array;
use function is_int;
use function is_string;

/**
 * Remembers the order of elements in the collection it is applied to
 * and tried to keep the order while merging.
 *
 * Usage only with merge. For example:
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'options' => [
 *         'count' => 42,
 *         'description' => null,
 *     ],
 *     'version' => '1.1',
 *     'meta' => ['date' => '19.11.2013'],
 * ];
 * $b = new ArrayCollection(
 *     [
 *         'version' => '3.0',
 *         'options' => [
 *             'count' => 97,
 *             'use' => true,
 *         ],
 *     ],
 *     new SaveOrder()
 * );
 *
 * // [
 * //     'version' => '3.0',
 * //     'options' => [
 * //         'count' => 97,
 * //         'description' => null,
 * //         'use' => true,
 * //     ],
 * //     'name' => 'Yii',
 * //     'meta' => ['date' => '19.11.2013'],
 * // ],
 * $result = ArrayHelper::merge($a, $b);
 * ```
 */
final class SaveOrder implements BeforeMergeModifierInterface, AfterMergeModifierInterface
{
    private array $array = [];

    private bool $nested = false;

    /**
     * Save order for nested arrays and collections.
     *
     * @return self
     */
    public function nested(): self
    {
        $new = clone $this;
        $new->nested = true;
        return $new;
    }

    /**
     * Save order only for top level of collection (default).
     *
     * @return self
     */
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
