<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

final class Post2
{
    public $id = 123;
    public $content = 'test';
    private $secret = 's';

    public function getSecret(): string
    {
        return $this->secret;
    }

    /** @noinspection MagicMethodsValidityInspection */
    public function __get($name)
    {
        return $this->$name;
    }
}
