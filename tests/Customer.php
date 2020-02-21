<?php

namespace Yiisoft\Arrays\Tests;

use ReflectionClass;

final class Customer
{
    public ?int $status2 = null;

    public ?int $sumTotal = null;

    public array $attributes = [
        'id' => 1,
        'email' => 'user1@example.com',
        'name' => 'user1',
        'address' => 'address1',
        'status' => 1,
        'profile_id' => 1,
    ];

    public array $oldAttributes = [
        'id' => 1,
        'email' => 'user1@example.com',
        'name' => 'user1',
        'address' => 'address1',
        'status' => 1,
        'profile_id' => 1,
    ];

    public function hasAttribute($name): bool
    {
        return isset($this->attributes[$name]) || in_array($name, $this->attributes(), true);
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name]) || array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if ($this->hasAttribute($name)) {
            return null;
        }
    }

    public function attributes(): array
    {
        $class = new ReflectionClass($this);
        $names = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }
}
