<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use Closure;

final class ArrayReader
{
    private array $array;

    private bool $convertEmptyToNull = true;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function withConvertEmptyToNull(): self
    {
        $new = clone $this;
        $new->convertEmptyToNull = true;
        return $new;
    }

    public function withNotConvertEmptyToNull(): self
    {
        $new = clone $this;
        $new->convertEmptyToNull = false;
        return $new;
    }

    /**
     * @param string|int|float|Closure|array $key
     * @param string $default
     * @return string
     */
    public function string($key, string $default = ''): string
    {
        return (string)$this->value($key, $default);
    }

    /**
     * @param string|int|float|Closure|array $path
     * @param string $default
     * @param string $delimiter
     * @return string
     */
    public function stringByPath($path, string $default = '', string $delimiter = '.'): string
    {
        return (string)$this->valueByPath($path, $default, $delimiter);
    }

    /**
     * @param string|int|float|Closure|array $key
     * @param string|null $default
     * @return string|null
     */
    public function stringOrNull($key, ?string $default = null): ?string
    {
        $value = $this->value($key, $default);
        return $value === null ? null : (string)$value;
    }

    /**
     * @param string|int|float|Closure|array $path
     * @param string|null $default
     * @param string $delimiter
     * @return string|null
     */
    public function stringOrNullByPath($path, string $default = null, string $delimiter = '.'): ?string
    {
        $value = $this->valueByPath($path, $default, $delimiter);
        return $value === null ? null : (string)$value;
    }

    /**
     * @param string|int|float|Closure|array $key
     * @param int $default
     * @return int
     */
    public function integer($key, int $default = 0): int
    {
        return (int)$this->value($key, $default);
    }

    /**
     * @param string|int|float|Closure|array $path
     * @param int $default
     * @param string $delimiter
     * @return int
     */
    public function integerByPath($path, int $default = 0, string $delimiter = '.'): int
    {
        return (int)$this->valueByPath($path, $default, $delimiter);
    }

    /**
     * @param string|int|float|Closure|array $key
     * @param int|null $default
     * @return int|null
     */
    public function integerOrNull($key, ?int $default = null): ?int
    {
        $value = $this->value($key, $default);
        return $value === null ? null : (int)$value;
    }

    /**
     * @param string|int|float|Closure|array $path
     * @param int|null $default
     * @param string $delimiter
     * @return int|null
     */
    public function integerOrNullByPath($path, ?int $default = null, string $delimiter = '.'): ?int
    {
        $value = $this->valueByPath($path, $default, $delimiter);
        return $value === null ? null : (int)$value;
    }

    /**
     * @param string|int|float|Closure|array $key
     * @param mixed $default
     * @return mixed
     */
    public function value($key, $default = null)
    {
        return $this->prepareValue(
            ArrayHelper::getValue($this->array, $key, $default)
        );
    }

    /**
     * @param string|int|float|Closure|array $path
     * @param mixed $default
     * @param string $delimiter
     * @return mixed
     */
    public function valueByPath($path, $default = null, string $delimiter = '.')
    {
        return $this->prepareValue(
            ArrayHelper::getValueByPath($this->array, $path, $default, $delimiter)
        );
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function prepareValue($value)
    {
        if ($this->convertEmptyToNull && empty($value)) {
            return null;
        }
        return $value;
    }
}
