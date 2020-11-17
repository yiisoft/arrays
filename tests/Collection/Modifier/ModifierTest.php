<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\Modifier\Modifier;

final class ModifierTest extends TestCase
{
    public function testGetPriority(): void
    {
        $modifier = (new ModifierStub())->withPriority(Modifier::PRIORITY_HIGH);
        $this->assertSame(Modifier::PRIORITY_HIGH, $modifier->getPriority());
    }

    public function testImmutability(): void
    {
        $modifier = new ModifierStub();
        $this->assertNotSame($modifier, $modifier->withPriority(Modifier::PRIORITY_NORMAL));
    }
}

class ModifierStub extends Modifier
{
}
