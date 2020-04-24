<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;

class ArraySorter
{
    /**
     * Sorts an array of objects or arrays (with the same structure) by one or several keys.
     * @param array $array the array to be sorted. The array will be modified after calling this method.
     * @param string|\Closure|array $key the key(s) to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](http://php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have
     * correct number of elements as that of $key.
     */
    public static function multisort(array &$array, $key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR): void
    {
        $keys = static::getKeys($array, $key);
        if (empty($keys)) {
            return;
        }

        $n = count($keys);
        if (is_scalar($direction)) {
            $direction = array_fill(0, $n, $direction);
        } elseif (count($direction) !== $n) {
            throw new InvalidArgumentException('The length of $direction parameter must be the same as that of $keys.');
        }

        if (is_scalar($sortFlag)) {
            $sortFlag = array_fill(0, $n, $sortFlag);
        } elseif (count($sortFlag) !== $n) {
            throw new InvalidArgumentException('The length of $sortFlag parameter must be the same as that of $keys.');
        }

        $args = static::getArguments($array, $keys, $direction, $sortFlag);

        $args[] = &$array;
        array_multisort(...$args);
    }

    /**
     * Get keys for get arguments
     *
     * @param array $array the array to be sorted. The array will be modified after calling this method.
     * @param string|\Closure|array $key the keys to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     *
     * @return Array return the keys
     */
    private static function getKeys(array &$array, $key): array
    {
        $keys = is_array($key) ? $key : [$key];
        if (empty($keys) || empty($array)) {
            return [];
        }

        if (is_callable($key)) {
            $keysTemp = ArrayHelper::getColumn($array, $key);
            // Check if the array is multidimensional
            if (count($keysTemp) !== count($keysTemp, COUNT_RECURSIVE)) {
                // If it is multidimensional then unify array and get keys
                $keys = array_unique($keysTemp, SORT_REGULAR)[0];
            }
        }

        return $keys;
    }

    /**
     * Get arguments for multisort
     *
     * @param array $array the array to be sorted. The array will be modified after calling this method.
     * @param string|\Closure|array $keys the keys to be sorted by. This refers to a key name of the sub-array
     * elements, a property name of the objects, or an anonymous function returning the values for comparison
     * purpose. The anonymous function signature should be: `function($item)`.
     * To sort by multiple keys, provide an array of keys here.
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * When sorting by multiple keys with different sorting directions, use an array of sorting directions.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](http://php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     *
     * @return Array return the arguments
     */
    private static function getArguments(array &$array, &$keys, $direction, $sortFlag): array
    {
        $args = [];
        foreach ($keys as $i => $iKey) {
            $flag = $sortFlag[$i];
            $args[] = ArrayHelper::getColumn($array, $iKey);
            $args[] = $direction[$i];
            $args[] = $flag;
        }

        // This fix is used for cases when main sorting specified by columns has equal values
        // Without it it will lead to Fatal Error: Nesting level too deep - recursive dependency?
        $args[] = range(1, count($array));
        $args[] = SORT_ASC;
        $args[] = SORT_NUMERIC;

        return $args;
    }
}
