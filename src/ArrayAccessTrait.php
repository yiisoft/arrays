<?php
namespace Yiisoft\Arrays;

/**
 * ArrayAccessTrait provides the implementation for [[\IteratorAggregate]], [[\ArrayAccess]] and [[\Countable]].
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
     * This method is required by the SPL interface [[\IteratorAggregate]].
     * It will be implicitly called when you use `foreach` to traverse the collection.
     * @return \ArrayIterator an iterator for traversing the cookies in the collection.
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Returns the number of data items.
     * This method is required by Countable interface.
     * @return int number of data elements.
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * This method is required by the interface [[\ArrayAccess]].
     * @param int $offset the offset to check on
     * @return bool
     */
    public function offsetExists(int $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * This method is required by the interface [[\ArrayAccess]].
     * @param int $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet(int $offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * This method is required by the interface [[\ArrayAccess]].
     * @param int $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet(int $offset, $item): void
    {
        $this->data[$offset] = $item;
    }

    /**
     * This method is required by the interface [[\ArrayAccess]].
     * @param int $offset the offset to unset element
     */
    public function offsetUnset(int $offset): void
    {
        unset($this->data[$offset]);
    }
}
