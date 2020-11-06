<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollectionIsImmutableException;

final class ArrayCollectionIsImmutableExceptionTest extends TestCase
{
    public function testMessage(): void
    {
        $this->expectExceptionMessage('ArrayCollection is immutable object.');
        throw new ArrayCollectionIsImmutableException();
    }
}
