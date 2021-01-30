<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use Closure;
use InvalidArgumentException;
use Throwable;
use Yiisoft\Arrays\Modifier\ModifierInterface;
use Yiisoft\Arrays\Modifier\ReverseBlockMerge;
use Yiisoft\Strings\NumericHelper;
use function array_key_exists;
use function get_class;
use function in_array;
use function is_array;
use function is_float;
use function is_int;
use function is_object;
use function is_string;

/**
 * Yii array helper provides static methods allowing you to deal with arrays more efficiently.
 */
class ArrayHelper
{
    /**
     * Converts an object or an array of objects into an array.
     *
     * For example:
     *
     * ```php
     * [
     *     Post::class => [
     *         'id',
     *         'title',
     *         'createTime' => 'created_at',
     *         'length' => function ($post) {
     *             return strlen($post->content);
     *         },
     *     ],
     * ]
     * ```
     *
     * The result of `ArrayHelper::toArray($post, $properties)` could be like the following:
     *
     * ```php
     * [
     *     'id' => 123,
     *     'title' => 'test',
     *     'createTime' => '2013-01-01 12:00AM',
     *     'length' => 301,
     * ]
     * ```
     *
     * @param array|object|string $object the object to be converted into an array.
     *
     * It is possible to provide default way of converting object to array for a specific class by implementing
     * `Yiisoft\Arrays\ArrayableInterface` interface in that class.
     * @param array $properties a mapping from object class names to the properties that need to put into the resulting arrays.
     * The properties specified for each class is an array of the following format:
     *
     * - A field name to include as is.
     * - A key-value pair of desired array key name and model column name to take value from.
     * - A key-value pair of desired array key name and a callback which returns value.
     * @param bool $recursive whether to recursively converts properties which are objects into arrays.
     *
     * @return array the array representation of the object
     */
    public static function toArray($object, array $properties = [], bool $recursive = true): array
    {
        if (is_array($object)) {
            if ($recursive) {
                /** @var mixed $value */
                foreach ($object as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $object[$key] = static::toArray($value, $properties);
                    }
                }
            }

            return $object;
        }

        if (is_object($object)) {
            if (!empty($properties)) {
                $className = get_class($object);
                if (!empty($properties[$className])) {
                    $result = [];
                    /**
                     * @var int|string $key
                     * @var string $name
                     */
                    foreach ($properties[$className] as $key => $name) {
                        if (is_int($key)) {
                            /** @var mixed */
                            $result[$name] = $object->$name;
                        } else {
                            /** @var mixed */
                            $result[$key] = static::getValue($object, $name);
                        }
                    }

                    return $recursive ? static::toArray($result, $properties) : $result;
                }
            }
            if ($object instanceof ArrayableInterface) {
                $result = $object->toArray([], [], $recursive);
            } else {
                $result = [];
                /**
                 * @var string $key
                 * @var mixed $value
                 */
                foreach ($object as $key => $value) {
                    /** @var mixed */
                    $result[$key] = $value;
                }
            }

            return $recursive ? static::toArray($result, $properties) : $result;
        }

        return [$object];
    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from `array_merge_recursive`).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * You can use modifiers {@see ArrayHelper::applyModifiers()} to change merging result.
     *
     * @param array ...$args arrays to be merged
     *
     * @return array the merged array (the original arrays are not changed)
     */
    public static function merge(...$args): array
    {
        $lastArray = end($args);
        if (
            isset($lastArray[ReverseBlockMerge::class]) &&
            $lastArray[ReverseBlockMerge::class] instanceof ReverseBlockMerge
        ) {
            return self::applyModifiers(self::performReverseBlockMerge(...$args));
        }

        return self::applyModifiers(self::performMerge(...$args));
    }

    private static function performMerge(array ...$args): array
    {
        $res = array_shift($args) ?: [];
        while (!empty($args)) {
            /** @psalm-var mixed $v */
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res) && $res[$k] !== $v) {
                        /** @var mixed */
                        $res[] = $v;
                    } else {
                        /** @var mixed */
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::performMerge($res[$k], $v);
                } else {
                    /** @var mixed */
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    private static function performReverseBlockMerge(array ...$args): array
    {
        $res = array_pop($args) ?: [];
        while (!empty($args)) {
            /** @psalm-var mixed $v */
            foreach (array_pop($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res) && $res[$k] !== $v) {
                        /** @var mixed */
                        $res[] = $v;
                    } else {
                        /** @var mixed */
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::performReverseBlockMerge($v, $res[$k]);
                } elseif (!isset($res[$k])) {
                    /** @var mixed */
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Apply modifiers (classes that implement {@link ModifierInterface}) in array.
     *
     * For example, {@link \Yiisoft\Arrays\Modifier\UnsetValue} to unset value from previous array or
     * {@link \Yiisoft\Arrays\ReplaceArrayValue} to force replace former value instead of recursive merging.
     *
     * @param array $data
     *
     * @return array
     *
     * @see ModifierInterface
     */
    public static function applyModifiers(array $data): array
    {
        $modifiers = [];
        /** @psalm-var mixed $v */
        foreach ($data as $k => $v) {
            if ($v instanceof ModifierInterface) {
                $modifiers[$k] = $v;
                unset($data[$k]);
            } elseif (is_array($v)) {
                $data[$k] = self::applyModifiers($v);
            }
        }
        ksort($modifiers);
        foreach ($modifiers as $key => $modifier) {
            $data = $modifier->apply($data, $key);
        }
        return $data;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * Below are some usage examples,
     *
     * ```php
     * // working with array
     * $username = \Yiisoft\Arrays\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \Yiisoft\Arrays\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \Yiisoft\Arrays\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using an array of keys to retrieve the value
     * $value = \Yiisoft\Arrays\ArrayHelper::getValue($versions, ['1.0', 'date']);
     * ```
     *
     * @param array|object $array array or object to extract value from
     * @param array|Closure|float|int|string $key key name of the array element,
     * an array of keys or property name of the object, or an anonymous function
     * returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     *
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof Closure) {
            return $key($array, $default);
        }

        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_array($array) && !is_object($array)) {
            throw new InvalidArgumentException(
                'getValue() can not get value from ' . gettype($array) . '. Only array and object are supported.'
            );
        }

        if (is_array($key)) {
            /** @psalm-var array<mixed,string|int> $key */
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                /** @var mixed */
                $array = static::getRootValue($array, $keyPart, $default);
            }
            return static::getRootValue($array, $lastKey, $default);
        }

        return static::getRootValue($array, $key, $default);
    }

    /**
     * @param mixed $array array or object to extract value from, otherwise method will return $default
     * @param float|int|string $key key name of the array element or property name of the object,
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     *
     * @return mixed the value of the element if found, default value otherwise
     */
    private static function getRootValue($array, $key, $default)
    {
        if (is_array($array)) {
            $key = static::normalizeArrayKey($key);
            return array_key_exists($key, $array) ? $array[$key] : $default;
        }

        if (is_object($array)) {
            try {
                return $array::$$key;
            } catch (Throwable $e) {
                // this is expected to fail if the property does not exist, or __get() is not implemented
                // it is not reliably possible to check whether a property is accessible beforehand
                return $array->$key;
            }
        }

        return $default;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot-separated format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays. So it is better to be done specifying an array of key names
     * like `['x', 'y', 'z']`.
     *
     * Below are some usage examples,
     *
     * ```php
     * // using separated format to retrieve the property of embedded object
     * $street = \Yiisoft\Arrays\ArrayHelper::getValue($users, 'address.street');
     * // using an array of keys to retrieve the value
     * $value = \Yiisoft\Arrays\ArrayHelper::getValue($versions, ['1.0', 'date']);
     * ```
     *
     * @param array|object $array array or object to extract value from
     * @param array|Closure|float|int|string $path key name of the array element, an array of keys or property name
     * of the object, or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @param string $delimiter a separator, used to parse string $key for embedded object property retrieving. Defaults
     * to "." (dot).
     *
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function getValueByPath($array, $path, $default = null, string $delimiter = '.')
    {
        return static::getValue($array, static::parsePath($path, $delimiter), $default);
    }

    /**
     * Writes a value into an associative array at the key path specified.
     * If there is no such key path yet, it will be created recursively.
     * If the key exists, it will be overwritten.
     *
     * ```php
     *  $array = [
     *      'key' => [
     *          'in' => [
     *              'val1',
     *              'key' => 'val'
     *          ]
     *      ]
     *  ];
     * ```
     *
     * The result of `ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);`
     * will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              'arr' => 'val'
     *          ]
     *      ]
     *  ]
     * ```
     *
     * @param array $array the array to write the value to
     * @param array|float|int|string|null $key the path of where do you want to write a value to `$array`
     * the path can be described by an array of keys
     * if the path is null then `$array` will be assigned the `$value`
     * @psalm-param array<mixed, string|int|float>|float|int|string|null $key
     *
     * @param mixed $value the value to be written
     */
    public static function setValue(array &$array, $key, $value): void
    {
        if ($key === null) {
            /** @var mixed */
            $array = $value;
            return;
        }

        $keys = is_array($key) ? $key : [$key];

        while (count($keys) > 1) {
            $k = static::normalizeArrayKey(array_shift($keys));
            if (!isset($array[$k])) {
                $array[$k] = [];
            }
            if (!is_array($array[$k])) {
                $array[$k] = [$array[$k]];
            }
            $array = &$array[$k];
        }

        /** @var mixed */
        $array[static::normalizeArrayKey(array_shift($keys))] = $value;
    }

    /**
     * Writes a value into an associative array at the key path specified.
     * If there is no such key path yet, it will be created recursively.
     * If the key exists, it will be overwritten.
     *
     * ```php
     *  $array = [
     *      'key' => [
     *          'in' => [
     *              'val1',
     *              'key' => 'val'
     *          ]
     *      ]
     *  ];
     * ```
     *
     * The result of `ArrayHelper::setValue($array, 'key.in.0', ['arr' => 'val']);` will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              ['arr' => 'val'],
     *              'key' => 'val'
     *          ]
     *      ]
     *  ]
     *
     * ```
     *
     * The result of
     * `ArrayHelper::setValue($array, 'key.in', ['arr' => 'val']);` or
     * `ArrayHelper::setValue($array, ['key', 'in'], ['arr' => 'val']);`
     * will be the following:
     *
     * ```php
     *  [
     *      'key' => [
     *          'in' => [
     *              'arr' => 'val'
     *          ]
     *      ]
     *  ]
     * ```
     *
     * @param array $array the array to write the value to
     * @param array|float|int|string|null $path the path of where do you want to write a value to `$array`
     * the path can be described by a string when each key should be separated by a dot
     * you can also describe the path as an array of keys
     * if the path is null then `$array` will be assigned the `$value`
     * @param mixed $value the value to be written
     * @param string $delimiter
     */
    public static function setValueByPath(array &$array, $path, $value, string $delimiter = '.'): void
    {
        static::setValue($array, static::parsePath($path, $delimiter), $value);
    }

    /**
     * @param mixed $path
     * @param string $delimiter
     *
     * @return mixed
     */
    private static function parsePath($path, string $delimiter)
    {
        if (is_string($path)) {
            return explode($delimiter, $path);
        }
        if (is_array($path)) {
            $newPath = [];
            foreach ($path as $key) {
                if (is_string($key) || is_array($key)) {
                    $newPath = array_merge($newPath, static::parsePath($key, $delimiter));
                } else {
                    $newPath[] = $key;
                }
            }
            return $newPath;
        }
        return $path;
    }

    /**
     * Removes an item from an array and returns the value. If the key does not exist in the array, the default value
     * will be returned instead.
     *
     * Usage examples,
     *
     * ```php
     * // $array = ['type' => 'A', 'options' => [1, 2]];
     * // working with array
     * $type = \Yiisoft\Arrays\ArrayHelper::remove($array, 'type');
     * // $array content
     * // $array = ['options' => [1, 2]];
     * ```
     *
     * @param array $array the array to extract value from
     * @param array|float|int|string $key key name of the array element or associative array at the key path specified
     * @psalm-param array<mixed, float|int|string>|float|int|string $key
     *
     * @param mixed $default the default value to be returned if the specified key does not exist
     *
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function remove(array &$array, $key, $default = null)
    {
        $keys = is_array($key) ? $key : [$key];

        while (count($keys) > 1) {
            $key = static::normalizeArrayKey(array_shift($keys));
            if (!isset($array[$key]) || !is_array($array[$key])) {
                return $default;
            }
            $array = &$array[$key];
        }

        $key = static::normalizeArrayKey(array_shift($keys));
        if (array_key_exists($key, $array)) {
            /** @var mixed */
            $value = $array[$key];
            unset($array[$key]);
            return $value;
        }

        return $default;
    }

    /**
     * Removes an item from an array and returns the value. If the key does not exist in the array, the default value
     * will be returned instead.
     *
     * Usage examples,
     *
     * ```php
     * // $array = ['type' => 'A', 'options' => [1, 2]];
     * // working with array
     * $type = \Yiisoft\Arrays\ArrayHelper::remove($array, 'type');
     * // $array content
     * // $array = ['options' => [1, 2]];
     * ```
     *
     * @param array $array the array to extract value from
     * @param array|string $path key name of the array element or associative array at the key path specified
     * the path can be described by a string when each key should be separated by a delimiter (default is dot)
     * @param mixed $default the default value to be returned if the specified key does not exist
     * @param string $delimiter
     *
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function removeByPath(array &$array, $path, $default = null, string $delimiter = '.')
    {
        return static::remove($array, static::parsePath($path, $delimiter), $default);
    }

    /**
     * Removes items with matching values from the array and returns the removed items.
     *
     * Example,
     *
     * ```php
     * $array = ['Bob' => 'Dylan', 'Michael' => 'Jackson', 'Mick' => 'Jagger', 'Janet' => 'Jackson'];
     * $removed = \Yiisoft\Arrays\ArrayHelper::removeValue($array, 'Jackson');
     * // result:
     * // $array = ['Bob' => 'Dylan', 'Mick' => 'Jagger'];
     * // $removed = ['Michael' => 'Jackson', 'Janet' => 'Jackson'];
     * ```
     *
     * @param array $array the array where to look the value from
     * @param mixed $value the value to remove from the array
     *
     * @return array the items that were removed from the array
     */
    public static function removeValue(array &$array, $value): array
    {
        $result = [];
        /** @psalm-var mixed $val */
        foreach ($array as $key => $val) {
            if ($val === $value) {
                /** @var mixed */
                $result[$key] = $val;
                unset($array[$key]);
            }
        }

        return $result;
    }

    /**
     * Indexes and/or groups the array according to a specified key.
     * The input should be either multidimensional array or an array of objects.
     *
     * The $key can be either a key name of the sub-array, a property name of object, or an anonymous
     * function that must return the value that will be used as a key.
     *
     * $groups is an array of keys, that will be used to group the input array into one or more sub-arrays based
     * on keys specified.
     *
     * If the `$key` is specified as `null` or a value of an element corresponding to the key is `null` in addition
     * to `$groups` not specified then the element is discarded.
     *
     * For example:
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'data' => 'abc', 'device' => 'laptop'],
     *     ['id' => '345', 'data' => 'def', 'device' => 'tablet'],
     *     ['id' => '345', 'data' => 'hgi', 'device' => 'smartphone'],
     * ];
     * $result = ArrayHelper::index($array, 'id');
     * ```
     *
     * The result will be an associative array, where the key is the value of `id` attribute
     *
     * ```php
     * [
     *     '123' => ['id' => '123', 'data' => 'abc', 'device' => 'laptop'],
     *     '345' => ['id' => '345', 'data' => 'hgi', 'device' => 'smartphone']
     *     // The second element of an original array is overwritten by the last element because of the same id
     * ]
     * ```
     *
     * An anonymous function can be used in the grouping array as well.
     *
     * ```php
     * $result = ArrayHelper::index($array, function ($element) {
     *     return $element['id'];
     * });
     * ```
     *
     * Passing `id` as a third argument will group `$array` by `id`:
     *
     * ```php
     * $result = ArrayHelper::index($array, null, 'id');
     * ```
     *
     * The result will be a multidimensional array grouped by `id` on the first level, by `device` on the second level
     * and indexed by `data` on the third level:
     *
     * ```php
     * [
     *     '123' => [
     *         ['id' => '123', 'data' => 'abc', 'device' => 'laptop']
     *     ],
     *     '345' => [ // all elements with this index are present in the result array
     *         ['id' => '345', 'data' => 'def', 'device' => 'tablet'],
     *         ['id' => '345', 'data' => 'hgi', 'device' => 'smartphone'],
     *     ]
     * ]
     * ```
     *
     * The anonymous function can be used in the array of grouping keys as well:
     *
     * ```php
     * $result = ArrayHelper::index($array, 'data', [function ($element) {
     *     return $element['id'];
     * }, 'device']);
     * ```
     *
     * The result will be a multidimensional array grouped by `id` on the first level, by the `device` on the second one
     * and indexed by the `data` on the third level:
     *
     * ```php
     * [
     *     '123' => [
     *         'laptop' => [
     *             'abc' => ['id' => '123', 'data' => 'abc', 'device' => 'laptop']
     *         ]
     *     ],
     *     '345' => [
     *         'tablet' => [
     *             'def' => ['id' => '345', 'data' => 'def', 'device' => 'tablet']
     *         ],
     *         'smartphone' => [
     *             'hgi' => ['id' => '345', 'data' => 'hgi', 'device' => 'smartphone']
     *         ]
     *     ]
     * ]
     * ```
     *
     * @param array $array the array that needs to be indexed or grouped
     * @psalm-param array<mixed, array|object> $array
     *
     * @param Closure|string|null $key the column name or anonymous function which result will be used to index the array
     * @param Closure[]|string|string[]|null $groups the array of keys, that will be used to group the input array
     * by one or more keys. If the $key attribute or its value for the particular element is null and $groups is not
     * defined, the array element will be discarded. Otherwise, if $groups is specified, array element will be added
     * to the result array without any key.
     *
     * @return array the indexed and/or grouped array
     */
    public static function index(array $array, $key, $groups = []): array
    {
        $result = [];
        $groups = (array)$groups;

        foreach ($array as $element) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!is_array($element) && !is_object($element)) {
                throw new InvalidArgumentException(
                    'index() can not get value from ' . gettype($element)
                    . '. The $array should be either multidimensional array or an array of objects.'
                );
            }

            $lastArray = &$result;

            foreach ($groups as $group) {
                $value = static::getValue($element, $group);
                if (!array_key_exists($value, $lastArray)) {
                    $lastArray[$value] = [];
                }
                $lastArray = &$lastArray[$value];
            }

            if ($key === null) {
                if (!empty($groups)) {
                    $lastArray[] = $element;
                }
            } else {
                $value = static::getValue($element, $key);
                if ($value !== null) {
                    $lastArray[static::normalizeArrayKey($value)] = $element;
                }
            }
            unset($lastArray);
        }

        return $result;
    }

    /**
     * Returns the values of a specified column in an array.
     * The input array should be multidimensional or an array of objects.
     *
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = ArrayHelper::getColumn($array, 'id');
     * // the result is: ['123', '345']
     *
     * // using anonymous function
     * $result = ArrayHelper::getColumn($array, function ($element) {
     *     return $element['id'];
     * });
     * ```
     *
     * @param array<array-key, array|object> $array
     *
     * @param Closure|string $name
     * @param bool $keepKeys whether to maintain the array keys. If false, the resulting array
     * will be re-indexed with integers.
     *
     * @return array the list of column values
     */
    public static function getColumn(array $array, $name, bool $keepKeys = true): array
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                /** @var mixed */
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                /** @var mixed */
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }

    /**
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     * Optionally, one can further group the map according to a grouping field `$group`.
     *
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
     *     ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
     *     ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
     * ];
     *
     * $result = ArrayHelper::map($array, 'id', 'name');
     * // the result is:
     * // [
     * //     '123' => 'aaa',
     * //     '124' => 'bbb',
     * //     '345' => 'ccc',
     * // ]
     *
     * $result = ArrayHelper::map($array, 'id', 'name', 'class');
     * // the result is:
     * // [
     * //     'x' => [
     * //         '123' => 'aaa',
     * //         '124' => 'bbb',
     * //     ],
     * //     'y' => [
     * //         '345' => 'ccc',
     * //     ],
     * // ]
     * ```
     *
     * @param array $array
     * @psalm-param array<mixed, array|object> $array
     *
     * @param Closure|string $from
     * @param Closure|string $to
     * @param Closure|string|null $group
     *
     * @return array
     */
    public static function map(array $array, $from, $to, $group = null): array
    {
        if ($group === null) {
            if ($from instanceof Closure || $to instanceof Closure) {
                $result = [];
                foreach ($array as $element) {
                    /** @var mixed */
                    $result[static::getValue($element, $from)] = static::getValue($element, $to);
                }

                return $result;
            }

            return array_column($array, $to, $from);
        }

        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            /** @var mixed */
            $result[static::getValue($element, $group)][$key] = static::getValue($element, $to);
        }

        return $result;
    }

    /**
     * Checks if the given array contains the specified key.
     * This method enhances the `array_key_exists()` function by supporting case-insensitive
     * key comparison.
     *
     * @param array $array the array with keys to check
     * @param array|float|int|string $key the key to check
     * @param bool $caseSensitive whether the key comparison should be case-sensitive
     *
     * @return bool whether the array contains the specified key
     */
    public static function keyExists(array $array, $key, bool $caseSensitive = true): bool
    {
        if (is_array($key)) {
            if (count($key) === 1) {
                return static::rootKeyExists($array, end($key), $caseSensitive);
            }

            foreach (self::getExistsKeys($array, array_shift($key), $caseSensitive) as $existKey) {
                /** @var mixed */
                $array = static::getRootValue($array, $existKey, null);
                if (is_array($array) && self::keyExists($array, $key, $caseSensitive)) {
                    return true;
                }
            }

            return false;
        }

        return static::rootKeyExists($array, $key, $caseSensitive);
    }

    /**
     * @param array $array
     * @param float|int|string $key
     * @param bool $caseSensitive
     *
     * @return bool
     */
    private static function rootKeyExists(array $array, $key, bool $caseSensitive): bool
    {
        $key = (string)$key;

        if ($caseSensitive) {
            return array_key_exists($key, $array);
        }

        foreach (array_keys($array) as $k) {
            if (strcasecmp($key, (string)$k) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $array
     * @param float|int|string $key
     * @param bool $caseSensitive
     *
     * @return array
     */
    private static function getExistsKeys(array $array, $key, bool $caseSensitive): array
    {
        $key = (string)$key;

        if ($caseSensitive) {
            return [$key];
        }

        return array_filter(
            array_keys($array),
            fn ($k) => strcasecmp($key, (string)$k) === 0
        );
    }

    /**
     * Checks if the given array contains the specified key. The key may be specified in a dot format.
     * In particular, if the key is `x.y.z`, then key would be `$array['x']['y']['z']`.
     *
     * This method enhances the `array_key_exists()` function by supporting case-insensitive
     * key comparison.
     *
     * @param array $array
     * @param array|float|int|string $path
     * @param bool $caseSensitive
     * @param string $delimiter
     *
     * @return bool
     */
    public static function pathExists(
        array $array,
        $path,
        bool $caseSensitive = true,
        string $delimiter = '.'
    ): bool {
        return static::keyExists($array, static::parsePath($path, $delimiter), $caseSensitive);
    }

    /**
     * Encodes special characters in an array of strings into HTML entities.
     * Only array values will be encoded by default.
     * If a value is an array, this method will also encode it recursively.
     * Only string values will be encoded.
     *
     * @param array $data data to be encoded
     * @psalm-param array<mixed, mixed> $data
     *
     * @param bool $valuesOnly whether to encode array values only. If false,
     * both the array keys and array values will be encoded.
     * @param string|null $encoding The encoding to use, defaults to `ini_get('default_charset')`.
     *
     * @return array the encoded data
     *
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function htmlEncode(array $data, bool $valuesOnly = true, string $encoding = null): array
    {
        $d = [];
        /** @var mixed $value */
        foreach ($data as $key => $value) {
            if (!$valuesOnly && is_string($key)) {
                /** @psalm-suppress PossiblyNullArgument */
                $key = htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, true);
            }
            if (is_string($value)) {
                /** @psalm-suppress PossiblyNullArgument */
                $d[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, true);
            } elseif (is_array($value)) {
                $d[$key] = static::htmlEncode($value, $valuesOnly, $encoding);
            } else {
                /** @var mixed */
                $d[$key] = $value;
            }
        }

        return $d;
    }

    /**
     * Decodes HTML entities into the corresponding characters in an array of strings.
     * Only array values will be decoded by default.
     * If a value is an array, this method will also decode it recursively.
     * Only string values will be decoded.
     *
     * @param array $data data to be decoded
     * @psalm-param array<mixed, mixed> $data
     *
     * @param bool $valuesOnly whether to decode array values only. If false,
     * both the array keys and array values will be decoded.
     *
     * @return array the decoded data
     *
     * @see https://www.php.net/manual/en/function.htmlspecialchars-decode.php
     */
    public static function htmlDecode(array $data, bool $valuesOnly = true): array
    {
        $d = [];
        /** @psalm-var mixed $value */
        foreach ($data as $key => $value) {
            if (!$valuesOnly && is_string($key)) {
                $key = htmlspecialchars_decode($key, ENT_QUOTES);
            }
            if (is_string($value)) {
                $d[$key] = htmlspecialchars_decode($value, ENT_QUOTES);
            } elseif (is_array($value)) {
                $d[$key] = static::htmlDecode($value);
            } else {
                /** @var mixed */
                $d[$key] = $value;
            }
        }

        return $d;
    }

    /**
     * Returns a value indicating whether the given array is an associative array.
     *
     * An array is associative if all its keys are strings. If `$allStrings` is false,
     * then an array will be treated as associative if at least one of its keys is a string.
     *
     * Note that an empty array will NOT be considered associative.
     *
     * @param array $array the array being checked
     * @param bool $allStrings whether the array keys must be all strings in order for
     * the array to be treated as associative.
     *
     * @return bool whether the array is associative
     */
    public static function isAssociative(array $array, bool $allStrings = true): bool
    {
        if ($array === []) {
            return false;
        }

        if ($allStrings) {
            /** @psalm-suppress MixedAssignment */
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }

            return true;
        }

        /** @psalm-suppress MixedAssignment */
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a value indicating whether the given array is an indexed array.
     *
     * An array is indexed if all its keys are integers. If `$consecutive` is true,
     * then the array keys must be a consecutive sequence starting from 0.
     *
     * Note that an empty array will be considered indexed.
     *
     * @param array $array the array being checked
     * @param bool $consecutive whether the array keys must be a consecutive sequence
     * in order for the array to be treated as indexed.
     *
     * @return bool whether the array is indexed
     */
    public static function isIndexed(array $array, bool $consecutive = false): bool
    {
        if ($array === []) {
            return true;
        }

        if ($consecutive) {
            return array_keys($array) === range(0, count($array) - 1);
        }

        /** @psalm-var mixed $value */
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether an array or `\Traversable` contains an element.
     *
     * This method does the same as the PHP function [in_array()](https://php.net/manual/en/function.in-array.php)
     * but additionally works for objects that implement the `\Traversable` interface.
     *
     * @param mixed $needle The value to look for.
     * @param iterable $haystack The set of values to search.
     * @param bool $strict Whether to enable strict (`===`) comparison.
     *
     * @throws InvalidArgumentException if `$haystack` is neither traversable nor an array.
     *
     * @return bool `true` if `$needle` was found in `$haystack`, `false` otherwise.
     *
     * @see https://php.net/manual/en/function.in-array.php
     */
    public static function isIn($needle, iterable $haystack, bool $strict = false): bool
    {
        if (is_array($haystack)) {
            return in_array($needle, $haystack, $strict);
        }

        /** @psalm-var mixed $value */
        foreach ($haystack as $value) {
            if ($needle == $value && (!$strict || $needle === $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks whether an array or `\Traversable` is a subset of another array or `\Traversable`.
     *
     * This method will return `true`, if all elements of `$needles` are contained in
     * `$haystack`. If at least one element is missing, `false` will be returned.
     *
     * @param iterable $needles The values that must **all** be in `$haystack`.
     * @param iterable $haystack The set of value to search.
     * @param bool $strict Whether to enable strict (`===`) comparison.
     *
     * @throws InvalidArgumentException if `$haystack` or `$needles` is neither traversable nor an array.
     *
     * @return bool `true` if `$needles` is a subset of `$haystack`, `false` otherwise.
     */
    public static function isSubset(iterable $needles, iterable $haystack, bool $strict = false): bool
    {
        /** @psalm-var mixed $needle */
        foreach ($needles as $needle) {
            if (!static::isIn($needle, $haystack, $strict)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Filters array according to rules specified.
     *
     * For example:
     *
     * ```php
     * $array = [
     *     'A' => [1, 2],
     *     'B' => [
     *         'C' => 1,
     *         'D' => 2,
     *     ],
     *     'E' => 1,
     * ];
     *
     * $result = \Yiisoft\Arrays\ArrayHelper::filter($array, ['A']);
     * // $result will be:
     * // [
     * //     'A' => [1, 2],
     * // ]
     *
     * $result = \Yiisoft\Arrays\ArrayHelper::filter($array, ['A', 'B.C']);
     * // $result will be:
     * // [
     * //     'A' => [1, 2],
     * //     'B' => ['C' => 1],
     * // ]
     *
     * $result = \Yiisoft\Arrays\ArrayHelper::filter($array, ['B', '!B.C']);
     * // $result will be:
     * // [
     * //     'B' => ['D' => 2],
     * // ]
     * ```
     *
     * @param array $array Source array
     * @param list<string> $filters Rules that define array keys which should be left or removed from results.
     * Each rule is:
     * - `var` - `$array['var']` will be left in result.
     * - `var.key` = only `$array['var']['key']` will be left in result.
     * - `!var.key` = `$array['var']['key']` will be removed from result.
     *
     * @return array Filtered array
     */
    public static function filter(array $array, array $filters): array
    {
        $result = [];
        $excludeFilters = [];

        foreach ($filters as $filter) {
            if ($filter[0] === '!') {
                $excludeFilters[] = substr($filter, 1);
                continue;
            }

            $nodeValue = $array; // set $array as root node
            $keys = explode('.', $filter);
            foreach ($keys as $key) {
                if (!is_array($nodeValue) || !array_key_exists($key, $nodeValue)) {
                    continue 2; // Jump to next filter
                }
                /** @var mixed */
                $nodeValue = $nodeValue[$key];
            }

            //We've found a value now let's insert it
            $resultNode = &$result;
            foreach ($keys as $key) {
                if (!array_key_exists($key, $resultNode)) {
                    $resultNode[$key] = [];
                }
                $resultNode = &$resultNode[$key];
            }
            /** @var mixed */
            $resultNode = $nodeValue;
        }

        /** @var array $result */

        foreach ($excludeFilters as $filter) {
            $excludeNode = &$result;
            $keys = explode('.', $filter);
            $numNestedKeys = count($keys) - 1;
            foreach ($keys as $i => $key) {
                if (!is_array($excludeNode) || !array_key_exists($key, $excludeNode)) {
                    continue 2; // Jump to next filter
                }

                if ($i < $numNestedKeys) {
                    /** @var mixed */
                    $excludeNode = &$excludeNode[$key];
                } else {
                    unset($excludeNode[$key]);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the public member variables of an object.
     * This method is provided such that we can get the public member variables of an object.
     * It is different from `get_object_vars()` because the latter will return private
     * and protected variables if it is called within the object itself.
     *
     * @param object $object the object to be handled
     *
     * @return array|null the public member variables of the object or null if not object given
     *
     * @see https://www.php.net/manual/en/function.get-object-vars.php
     */
    public static function getObjectVars(object $object): ?array
    {
        return get_object_vars($object);
    }

    /**
     * @param float|int|string $key
     *
     * @return string
     */
    private static function normalizeArrayKey($key): string
    {
        return is_float($key) ? NumericHelper::normalize($key) : (string)$key;
    }
}
