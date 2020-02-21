<?php

namespace Yiisoft\Arrays\Tests;

final class Customer
{
    public $status2 = null;

    public $sumTotal = null;

    public $attributes = [
        'id' => 1,
        'email' => 'user1@example.com',
        'name' => 'user1',
        'address' => 'address1',
        'status' => 1,
        'profile_id' => 1,
    ];

    public $oldAttributes = [
        'id' => 1,
        'email' => 'user1@example.com',
        'name' => 'user1',
        'address' => 'address1',
        'status' => 1,
        'profile_id' => 1,
    ];

    public function hasAttribute($name)
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

        if (isset($this->related[$name]) || array_key_exists($name, $this->related)) {
            return $this->related[$name];
        }

        return $value;
    }

    public function attributes()
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
