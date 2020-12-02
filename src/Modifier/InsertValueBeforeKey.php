<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Modifier;

/**
 * Inserts given value before specified key while performing {@see ArrayHelper::merge()}.
 *
 * The modifier should be specified as
 *
 * ```php
 * 'some-key' => new InsertValueBeforeKey('some-value', 'a-key-to-insert-before'),
 * ```
 *
 * ```php
 * $a = [
 *     'name' => 'Yii',
 *     'version' => '1.0',
 * ];
 *
 * $b = [
 *    'version' => '1.1',
 *    'options' => [],
 *    'vendor' => new InsertValueBeforeKey('Yiisoft', 'name'),
 * ];
 *
 * $result = ArrayHelper::merge($a, $b);
 * ```
 *
 * Will result in:
 *
 * ```php
 * [
 *     'vendor' => 'Yiisoft',
 *     'name' => 'Yii',
 *     'version' => '1.1',
 *     'options' => [],
 * ];
 */
final class InsertValueBeforeKey implements ModifierInterface
{
    /** @var mixed value of any type */
    private $value;

    private string $key;

    /**
     * @param mixed $value value of any type
     * @param string $key
     */
    public function __construct($value, string $key)
    {
        $this->value = $value;
        $this->key = $key;
    }

    public function apply(array $data, $key): array
    {
        $res = [];
        /** @psalm-var mixed $v */
        foreach ($data as $k => $v) {
            if ($k === $this->key) {
                /** @var mixed */
                $res[$key] = $this->value;
            }
            /** @var mixed */
            $res[$k] = $v;
        }

        return $res;
    }
}
