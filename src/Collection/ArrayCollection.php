<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Yiisoft\Arrays\ArrayAccessTrait;
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

    public function pullCollectionArgs(ArrayCollection $collection): void
    {
        $this->modifiers = array_merge($this->modifiers, $collection->modifiers);
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param int|string $key
     * @return bool
     */
    public function keyExists($key): bool
    {
        return array_key_exists($key, $this->data);
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
