<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

use function array_slice;

/**
 * The modifier allows to mark an array element from the collection it is applied to,
 * as the element to be processed in a special way on merge.
 *
 * - In case there are elements with the same keys in previous arrays, they will be replaced
 *   with a value from the current array.
 *
 * - If there are elements with the same keys in next arrays, they will replace current array value.
 *
 * If there is no element with the given key in the array, modifier won't change anything.
 *
 * Note that this modifier is applied on merge.
 *
 * Usage example:
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.1',
 *     'features' => ['mvc'],
 * ];
 * $b = new ArrayCollection(
 *     [
 *         'version' => '3.0',
 *         'features' => ['gii'],
 *     ],
 *     new ReplaceValue('features')
 * );
 *
 * // [
 * //     'name' => 'Yii',
 * //     'version' => '3.0',
 * //     'features' => ['gii'],
 * // ],
 * $result = ArrayHelper::merge($a, $b));
 * ```
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
