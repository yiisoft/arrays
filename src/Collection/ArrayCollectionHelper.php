<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use InvalidArgumentException;
use Yiisoft\Arrays\Collection\Modifier\MergeWithKeysAsReverseMerge;

final class ArrayCollectionHelper
{
    public static function merge(...$args): ArrayCollection
    {
        $lastArray = end($args);
        if (
            $lastArray instanceof ArrayCollection &&
            static::hasMergeWithKeysAsReverseMergeModifier($lastArray)
        ) {
            return static::mergeWithKeysAsReverseMerge(...$args);
        }

        return static::mergeBase(...$args);
    }

    private static function hasMergeWithKeysAsReverseMergeModifier(ArrayCollection $collection): bool
    {
        foreach ($collection->getModifiers() as $modifier) {
            if ($modifier instanceof MergeWithKeysAsReverseMerge) {
                return true;
            }
        }
        return false;
    }

    private static function mergeBase(...$args): ArrayCollection
    {
        $collection = new ArrayCollection();

        while (!empty($args)) {
            $array = array_shift($args);

            if ($array instanceof ArrayCollection) {
                $collection->pullCollectionArgs($array);
                $collection->setData(
                    static::mergeBase($collection->getData(), $array->getData())->getData()
                );
                continue;
            } elseif (!is_array($array)) {
                throw new InvalidArgumentException();
            }

            foreach ($array as $k => $v) {
                if (is_int($k)) {
                    if ($collection->keyExists($k)) {
                        if ($collection[$k] !== $v) {
                            $collection[] = $v;
                        }
                    } else {
                        $collection[$k] = $v;
                    }
                } elseif (static::isMergable($v) && isset($collection[$k]) && static::isMergable($collection[$k])) {
                    $collection[$k] = static::mergeBase($collection[$k], $v)->getData();
                } else {
                    $collection[$k] = $v;
                }
            }
        }

        return $collection;
    }

    private static function mergeWithKeysAsReverseMerge(...$args): ArrayCollection
    {
        $collection = new ArrayCollection();

        while (!empty($args)) {
            $array = array_pop($args);

            if ($array instanceof ArrayCollection) {
                $collection->pullCollectionArgs($array);
                $collection->setData(
                    static::mergeWithKeysAsReverseMerge($collection->getData(), $array->getData())->getData()
                );
                continue;
            } elseif (!is_array($array)) {
                throw new InvalidArgumentException();
            }

            foreach ($array as $k => $v) {
                if (is_int($k)) {
                    if ($collection->keyExists($k)) {
                        if ($collection[$k] !== $v) {
                            $collection[] = $v;
                        }
                    } else {
                        $collection[$k] = $v;
                    }
                } elseif (static::isMergable($v) && isset($collection[$k]) && static::isMergable($collection[$k])) {
                    $collection[$k] = static::mergeWithKeysAsReverseMerge($v, $collection[$k]);
                } elseif (!$collection->keyExists($k)) {
                    $collection[$k] = $v;
                }
            }
        }

        return $collection;
    }

    private static function isMergable($value): bool
    {
        return is_array($value) || $value instanceof ArrayCollection;
    }
}
