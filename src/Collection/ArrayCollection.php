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

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return ModifierInterface[]
     */
    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    /**
     * @param ModifierInterface[] $modifiers
     * @return self
     */
    public function setModifiers(array $modifiers): self
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    public function addModifier(ModifierInterface ...$modifiers): self
    {
        $this->modifiers = array_merge($this->modifiers, $modifiers);
        return $this;
    }

    /**
     * @param ModifierInterface[] $modifiers
     * @return self
     */
    public function addModifiers(array $modifiers): self
    {
        $this->modifiers = array_merge($this->modifiers, $modifiers);
        return $this;
    }

    /**
     * @param int|string $key
     * @return bool
     */
    public function keyExists($key): bool
    {
        return array_key_exists($key, $this->data);
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
                    if ($collection->keyExists($k)) {
                        if ($collection[$k] !== $v) {
                            $collection[] = $v;
                        }
                    } else {
                        $collection[$k] = $v;
                    }
                } elseif (static::isMergable($v) && isset($collection[$k]) && static::isMergable($collection[$k])) {
                    $collection[$k] = $this->merge($collection[$k], $v)->data;
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
}
