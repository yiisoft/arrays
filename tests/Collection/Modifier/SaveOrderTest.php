<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\SaveOrder;

final class SaveOrderTest extends TestCase
{
    public function testBase(): void
    {
        $a = [
            'name' => 'Yii',
            'options' => [
                'option1' => 'valueA',
                'option3' => 'valueAA',
            ],
            'version' => '1.1',
            'meta' => ['a' => 1],
        ];
        $b = new ArrayCollection(
            [
                'version' => '3.0',
                'options' => [
                    'option1' => 'valueB',
                    'option2' => 'valueBB',
                ],
            ],
            new SaveOrder()
        );

        $this->assertSame(
            [
                'version' => '3.0',
                'options' => [
                    'option1' => 'valueB',
                    'option2' => 'valueBB',
                    'option3' => 'valueAA',
                ],
                'name' => 'Yii',
                'meta' => ['a' => 1],
            ],
            ArrayHelper::merge($a, $b)
        );
    }

    public function testWithIntKeys(): void
    {
        $a = ['A', 'B'];
        $b = new ArrayCollection(
            ['C', 'B', 'D', 'B'],
            new SaveOrder()
        );

        $this->assertSame(
            ['C', 'B', 'D', 'B', 'A'],
            ArrayHelper::merge($a, $b)
        );
    }

    public function testSeveralModifiers(): void
    {
        $a = new ArrayCollection(
            ['a' => 1, 'b' => 2],
            new SaveOrder()
        );
        $b = new ArrayCollection(
            ['c' => 1, 'a' => [2, 3]],
            new SaveOrder()
        );

        $this->assertSame(
            ['c' => 1, 'a' => [2, 3], 'b' => 2],
            ArrayHelper::merge($a, $b)
        );
    }
}
