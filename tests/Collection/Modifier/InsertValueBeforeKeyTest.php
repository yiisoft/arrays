<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\InsertValueBeforeKey;

final class InsertValueBeforeKeyTest extends TestCase
{
    public function testBase(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
        ];
        $b = new ArrayCollection(
            [
                'version' => '3.0',
                'options' => [],
                'vendor' => 'Yiisoft',
            ],
            new InsertValueBeforeKey('vendor', 'name')
        );

        $this->assertSame(
            [
                'vendor' => 'Yiisoft',
                'name' => 'Yii',
                'version' => '3.0',
                'options' => [],
            ],
            ArrayHelper::merge($a, $b)
        );
    }

    public function testWithKey(): void
    {
        $modifierA = new InsertValueBeforeKey('a', 'c');
        $modifierD = $modifierA->withKey('d');

        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertSame(['b' => 2, 'a' => 1, 'c' => 3, 'd' => 4], $modifierA->apply($array));
        $this->assertSame(['a' => 1, 'b' => 2, 'd' => 4, 'c' => 3], $modifierD->apply($array));
    }

    public function testBeforeKey(): void
    {
        $modifierA = new InsertValueBeforeKey('a', 'c');
        $modifierD = $modifierA->beforeKey('d');

        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertSame(['b' => 2, 'a' => 1, 'c' => 3, 'd' => 4], $modifierA->apply($array));
        $this->assertSame(['b' => 2, 'c' => 3, 'a' => 1, 'd' => 4], $modifierD->apply($array));
    }

    public function testWithoutKey(): void
    {
        $modifier = new InsertValueBeforeKey('x', 'c');

        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertSame($array, $modifier->apply($array));
    }
}
