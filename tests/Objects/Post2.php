<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Objects;

final class Post2
{
    public $id = 123;
    public $content = 'test';
    private string $secret = 's';

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
