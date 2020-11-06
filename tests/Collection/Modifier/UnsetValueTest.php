<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\UnsetValue;

final class UnsetValueTest extends TestCase
{
    public function testBase(): void
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = new ArrayCollection(
            [
                'version' => '3.0',
                'features' => [
                    'gii',
                ],
            ],
            new UnsetValue('options')
        );

        $this->assertEquals(
            [
                'name' => 'Yii',
                'version' => '3.0',
                'features' => [
                    'mvc',
                    'gii',
                ],
            ],
            ArrayHelper::merge($a, $b)
        );
    }

    public function testWithKey(): void
    {
        $modifierA = new UnsetValue('a');
        $modifierB = $modifierA->withKey('b');

        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertSame(['b' => 2, 'c' => 3], $modifierA->apply($array));
        $this->assertSame(['a' => 1, 'c' => 3], $modifierB->apply($array));
    }
}
