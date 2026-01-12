<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use Closure;
use InvalidArgumentException;

use function array_multisort;
use function count;
use function is_array;

use const SORT_ASC;
use const SORT_NUMERIC;
use const SORT_REGULAR;

final class ArraySorter
{
    /**
     * Sorts an array of objects or arrays (with the same structure) by one or several keys.
     *
     * For example:
     *
     * ```php
     * $data = [
     *     ['age' => 30, 'name' => 'Alexander'],
     *     ['age' => 30, 'name' => 'Brian'],
     *     ['age' => 19, 'name' => 'Barney'],
     * ];
     * ArraySorter::multisort($data, ['age', 'name'], [SORT_ASC, SORT_DESC]);
     * ```
     *
     * After sorting, we'll get the following in `$data`:
     *
     * ```php
     * [
     *     ['age' => 19, 'name' => 'Barney'],
     *     ['age' => 30, 'name' => 'Brian'],
     *     ['age' => 30, 'name' => 'Alexander'],
     * ];
     * ```
     *
     * @param array<array-key, array|object> $array The array to be sorted. The array will be modified after calling
     * this method.
     * @param array<array-key, Closure|string>|Closure|string $key The key(s) to be sorted by. This refers to a key
     * name of the subarray elements, a property name of the objects, or an anonymous function returning the values
     * for comparison purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param array<array-key, int>|int $direction The sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param array<array-key, int>|int $sortFlag The PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](https://php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     *
     * @throws InvalidArgumentException If the `$direction` or `$sortFlag` parameters do not have
     * correct number of elements as that of $key.`
     */
    public static function multisort(
        array &$array,
        array|Closure|string $key,
        array|int $direction = SORT_ASC,
        array|int $sortFlag = SORT_REGULAR,
    ): void {
        $count = count($array);
        if ($count === 0) {
            return;
        }

        $keys = is_array($key) ? $key : [$key];
        $keysCount = count($keys);
        if ($keysCount === 0) {
            return;
        }

        if (is_array($direction) && count($direction) !== $keysCount) {
            throw new InvalidArgumentException('The length of $direction parameter must be the same as that of $keys.');
        }
        if (is_array($sortFlag) && count($sortFlag) !== $keysCount) {
            throw new InvalidArgumentException('The length of $sortFlag parameter must be the same as that of $keys.');
        }

        $args = [];

        for ($i = 0; $i < $keysCount; $i++) {
            $args[] = ArrayHelper::getColumn($array, $keys[$i]);
            $args[] = is_array($direction) ? $direction[$i] : $direction;
            $args[] = is_array($sortFlag) ? $sortFlag[$i] : $sortFlag;
        }

        // Add tie-breaker only for non-empty arrays
        if ($count > 0) {
            $tieBreaker = [];
            for ($i = 0; $i < $count; $i++) {
                $tieBreaker[$i] = $i + 1;
            }
            $args[] = $tieBreaker;
            $args[] = SORT_ASC;
            $args[] = SORT_NUMERIC;
        }

        /** @psalm-suppress UnsupportedReferenceUsage */
        $args[] = &$array;

        /** @psalm-suppress ArgumentTypeCoercion */
        array_multisort(...$args);
    }
}
