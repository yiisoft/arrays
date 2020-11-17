<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Move element with a key `key` before an element with `beforeKey` key.
 *
 * Simple usage:
 *
 * ```php
 * $modifier = new MoveValueBeforeKey('a', 'c');
 *
 * // ['b' => 2, 'a' => 1, 'c' => 3]
 * $result = $modifier->apply(['a' => 1, 'b' => 2, 'c' => 3])
 * ```
 *
 * Usage with merge:
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.0',
 * ];
 * $b = new ArrayCollection(
 *     [
 *         'version' => '3.0',
 *         'options' => [],
 *         'vendor' => 'Yiisoft',
 *     ],
 *     new MoveValueBeforeKey('vendor', 'name')
 * );
 *
 * // [
 * //     'vendor' => 'Yiisoft',
 * //     'name' => 'Yii',
 * //     'version' => '3.0',
 * //     'options' => [],
 * // ],
 * $result = ArrayHelper::merge($a, $b);
 * ```
 */
final class MoveValueBeforeKey extends Modifier implements DataModifierInterface
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
