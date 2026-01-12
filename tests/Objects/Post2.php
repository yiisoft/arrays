<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

final class Post2
{
    public $id = 123;
    public string $content = 'test';
    private string $secret = 's';

    /** @noinspection MagicMethodsValidityInspection */
    public function __get($name)
    {
        return $this->$name;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }
}
