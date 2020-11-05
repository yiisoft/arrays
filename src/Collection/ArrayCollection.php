<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Yiisoft\Arrays\ArrayAccessTrait;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\AfterMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\BeforeMergeModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;
use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\ModifierInterface;

final class ArrayCollection implements ArrayAccess, IteratorAggregate, Countable
{
    use ArrayAccessTrait;

    private array $data;

    /**
     * @var ModifierInterface[]
     */
    private array $modifiers = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
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

    public function withModifier(ModifierInterface ...$modifiers): self
    {
        return $this->withModifiers($modifiers);
    }

    /**
     * @param ModifierInterface[] $modifiers
     * @return self
     */
    public function withModifiers(array $modifiers): self
    {
        $new = clone $this;
        $new->modifiers = $modifiers;
        return $new;
    }

    public function withAddedModifier(ModifierInterface ...$modifiers): self
    {
        return $this->withAddedModifiers($modifiers);
    }

    /**
     * @param ModifierInterface[] $modifiers
     * @return self
     */
    public function withAddedModifiers(array $modifiers): self
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
                        if ($collection[$k] !== $v) {
                            $collection->data[] = $v;
                        }
                    } else {
                        $collection->data[$k] = $v;
                    }
                } elseif (static::isMergable($v) && isset($collection[$k]) && static::isMergable($collection[$k])) {
                    $collection->data[$k] = $this->merge($collection[$k], $v)->data;
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
    private function isMergable($value): bool
    {
        return is_array($value) || $value instanceof self;
    }

    public function toArray(): array
    {
        $array = $this->performArray($this->getIterator()->getArrayCopy());

        foreach ($this->modifiers as $modifier) {
            if ($modifier instanceof DataModifierInterface) {
                $array = $modifier->apply($array);
            }
        }

        return $array;
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
}
