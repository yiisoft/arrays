<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use InvalidArgumentException;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;

final class ArrayCollectionHelper
{
    /**
     * @param array|ArrayCollection ...$args
     * @return ArrayCollection
     */
    public static function merge(...$args): ArrayCollection
    {
        $arrays = [];
        foreach ($args as $arg) {
            $arrays[] = $arg instanceof ArrayCollection ? $arg->getData() : $arg;
        }

        $collections = [];
        foreach ($args as $index => $arg) {
            $collection = $arg instanceof ArrayCollection ? $arg : new ArrayCollection($arg);
            foreach ($collection->getModifiers() as $modifier) {
                if ($modifier instanceof BeforeMergeModifierInterface) {
                    $collection->setData(
                        $modifier->beforeMerge($arrays, $index)
                    );
                }
            }
            $collections[$index] = $collection;
        }

        $collection = static::mergeBase(...$collections);

        foreach ($collection->getModifiers() as $modifier) {
            if ($modifier instanceof AfterMergeModifierInterface) {
                $collection->setData($modifier->afterMerge($collection->getData()));
            }
        }

        return $collection;
    }

    /**
     * @param array|ArrayCollection ...$args
     * @return ArrayCollection
     */
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

    /**
     * @param mixed $value
     * @return bool
     */
    private static function isMergable($value): bool
    {
        return is_array($value) || $value instanceof ArrayCollection;
    }
}
