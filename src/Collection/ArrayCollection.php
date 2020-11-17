<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\ModifierInterface;

use function count;
use function is_array;

/**
 * Array wrapper that allows specifying modifiers. When you get array value or whole array
 * from the collection modifiers are applied first so you get modified data.
 *
 * When merging collections using `ArrayHelper::merge()` or `$collection->mergeWith()` original arrays
 * and modifers are merged separately.
 */
final class ArrayCollection implements ArrayAccess, IteratorAggregate, Countable
{
    private array $data;

    /**
     * @var array|null Result array cache
     */
    private ?array $array = null;

    /**
     * @var ModifierInterface[]
     */
    private array $modifiers;

    public function __construct(array $data = [], ModifierInterface ...$modifiers)
    {
        $this->data = $data;
        $this->modifiers = $modifiers;
    }

    public function withData(array $data): self
    {
        $new = clone $this;
        $new->data = $data;
        return $new;
    }

    /**
     * @return ModifierInterface[]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function withModifiers(ModifierInterface ...$modifiers): self
    {
        $new = clone $this;
        $new->modifiers = $modifiers;
        return $new;
    }

    public function withAddedModifiers(ModifierInterface ...$modifiers): self
    {
        $new = clone $this;
        $new->modifiers = array_merge($new->modifiers, $modifiers);
        return $new;
    }

    /**
     * @param array|self ...$args
     * @return self
     */
    public function mergeWith(...$args): self
    {
        array_unshift($args, $this);

        $arrays = [];
        foreach ($args as $arg) {
            $arrays[] = $arg instanceof self ? $arg->data : $arg;
        }

        $collections = [];
        foreach ($args as $index => $arg) {
            $collection = $arg instanceof self ? $arg : new self($arg);
            foreach ($collection->getModifiers() as $modifier) {
                if ($modifier instanceof BeforeMergeModifierInterface) {
                    $collection->data = $modifier->beforeMerge($arrays, $index);
                }
            }
            $collections[$index] = $collection;
        }

        $collection = $this->merge(...$collections);

        foreach ($collection->getModifiers() as $modifier) {
            if ($modifier instanceof AfterMergeModifierInterface) {
                $collection->data = $modifier->afterMerge($collection->data);
            }
        }

        return $collection;
    }

    /**
     * @param array|self ...$args
     * @return self
     */
    private function merge(...$args): self
    {
        $collection = new ArrayCollection();

        while (!empty($args)) {
            $array = array_shift($args);

            if ($array instanceof ArrayCollection) {
                $collection->modifiers = array_merge($collection->modifiers, $array->modifiers);
                $collection->data = $this->merge($collection->data, $array->data)->data;
                continue;
            }

            foreach ($array as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $collection->data)) {
                        if ($collection->data[$k] !== $v) {
                            $collection->data[] = $v;
                        }
                    } else {
                        $collection->data[$k] = $v;
                    }
                } elseif (
                    isset($collection->data[$k]) &&
                    static::isMergeable($v) &&
                    static::isMergeable($collection->data[$k])
                ) {
                    $mergedCollection = $this->merge($collection->data[$k], $v);
                    $collection->data[$k] = ($collection->data[$k] instanceof self || $v instanceof self)
                        ? $mergedCollection
                        : $mergedCollection->data;
                } else {
                    $collection->data[$k] = $v;
                }
            }
        }

        return $collection;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isMergeable($value): bool
    {
        return is_array($value) || $value instanceof self;
    }

    public function toArray(): array
    {
        if ($this->array === null) {
            $this->array = $this->performArray($this->data);

            $modifiers = $this->modifiers;
            usort($modifiers, function (ModifierInterface $a, ModifierInterface $b) {
                return $b->getPriority() <=> $a->getPriority();
            });

            foreach ($modifiers as $modifier) {
                if ($modifier instanceof DataModifierInterface) {
                    $this->array = $modifier->apply($this->array);
                }
            }
        }
        return $this->array;
    }

    private function performArray(array $array): array
    {
        foreach ($array as $k => $v) {
            if ($v instanceof ArrayCollection) {
                $array[$k] = $v->toArray();
            } elseif (is_array($v)) {
                $array[$k] = $this->performArray($v);
            } else {
                $array[$k] = $v;
            }
        }
        return $array;
    }

    /**
     * Returns an iterator for traversing the data.
     * This method is required by the SPL interface {@see IteratorAggregate}.
     * It will be implicitly called when you use `foreach` to traverse the collection.
     * @return ArrayIterator an iterator for traversing the cookies in the collection.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Returns the number of data items.
     * This method is required by {@see Countable} interface.
     * @return int number of data elements.
     */
    public function count(): int
    {
        return count($this->toArray());
    }

    /**
     * This method is required by the interface {@see ArrayAccess}.
     * @param mixed $offset the offset to check on
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    /**
     * This method is required by the interface {@see ArrayAccess}.
     * @param mixed $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return $this->toArray()[$offset] ?? null;
    }

    /**
     * @param mixed $offset the offset to set element
     * @param mixed $value the element value
     */
    public function offsetSet($offset, $value): void
    {
        throw new ArrayCollectionIsImmutableException();
    }

    /**
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset): void
    {
        throw new ArrayCollectionIsImmutableException();
    }

    public function __clone()
    {
        $this->array = null;
    }
}
