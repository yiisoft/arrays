<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Modifier\ReplaceValue;
use Yiisoft\Arrays\Modifier\UnsetValue;

/**
 * @see MergeTest
 */
final class ApplyModifiersTest extends TestCase
{
    public function testBase(): void
    {
        $array = [
            'a' => 1,
            'b' => new ReplaceValue(2),
            'c' => new UnsetValue(),
            2 => new UnsetValue(),
        ];

        $this->assertSame([
            'a' => 1,
            'b' => 2,
        ], ArrayHelper::applyModifiers($array));
    }
}
