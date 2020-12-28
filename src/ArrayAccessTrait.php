<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

use ArrayIterator;

/**
 * ArrayAccessTrait provides the implementation for {@see \IteratorAggregate}, {@see \ArrayAccess} and {@see \Countable}.
 *
 * Note that ArrayAccessTrait requires the class using it contain a property named `data` which should be an array.
 * The data will be exposed by ArrayAccessTrait to support accessing the class object like an array.
 *
 * @property array $data
 */
trait ArrayAccessTrait
{
    /**
     * Returns an iterator for traversing the data.
     * This method is required by the SPL interface {@see \IteratorAggregate}.
     * It will be implicitly called when you use `foreach` to traverse the collection.
     *
     * @return ArrayIterator an iterator for traversing the cookies in the collection.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Returns the number of data items.
     * This method is required by Countable interface.
     *
     * @return int number of data elements.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * This method is required by the interface {@see \ArrayAccess}.
     *
     * @param mixed $offset the offset to check on
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * This method is required by the interface {@see \ArrayAccess}.
     *
     * @param mixed $offset the offset to retrieve element.
     *
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * This method is required by the interface {@see \ArrayAccess}.
     *
     * @param mixed $offset the offset to set element
     * @param mixed $value the element value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * This method is required by the interface {@see \ArrayAccess}.
     *
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
}
