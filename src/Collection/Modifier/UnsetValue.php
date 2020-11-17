<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Removes an array element with a given key.
 *
 * Simple usage:
 *
 * ```php
 * $unsetCode = new UnsetValue('code');
 *
 * // ['message' => 'Success']
 * $result = $unsetCode->apply(['code' => 42, 'message' => 'Success']);
 * ```
 *
 * Usage with merge:
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.1',
 *     'options' => [
 *         'namespace' => false,
 *         'unittest' => false,
 *     ],
 *     'features' => [
 *         'mvc',
 *     ],
 * ];
 * $b = new ArrayCollection(
 *     [
 *         'version' => '3.0',
 *         'features' => [
 *             'gii',
 *         ],
 *     ],
 *    new UnsetValue('options')
 * );
 *
 * // [
 * //     'name' => 'Yii',
 * //     'version' => '3.0',
 * //     'features' => [
 * //         'mvc',
 * //         'gii',
 * //     ],
 * // ]
 * $result = ArrayHelper::merge($a, $b);
 * ```
 */
final class UnsetValue implements DataModifierInterface
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
    public function withKey($key): self
    {
        $new = clone $this;
        $new->key = $key;
        return $new;
    }

    public function apply(array $data): array
    {
        unset($data[$this->key]);
        return $data;
    }
}
