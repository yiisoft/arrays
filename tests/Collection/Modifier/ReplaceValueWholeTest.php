<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\ArrayCollectionHelper;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValueWhole;

final class ReplaceValueWholeTest extends TestCase
{
    public function testBase(): void
    {
        $a = (new ArrayCollection())
            ->addModifier(new ReplaceValueWhole('options'))
            ->setData([
                'name' => 'Yii',
                'version' => '1.0',
                'options' => [
                    'namespace' => false,
                    'unittest' => false,
                ],
            ]);
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $expected = [
            'name' => 'Yii',
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];

        $this->assertEquals($expected, ArrayCollectionHelper::merge($a, $b)->toArray());
    }

    public function testForKey(): void
    {
        $array = ['x' => [1, 2], 'y' => [3, 4]];
        $allArrays = [
            $array,
            ['x' => [5], 'y' => [6]],
        ];

        $modifierX = new ReplaceValueWhole('x');
        $modifierY = $modifierX->forKey('y');

        $this->assertSame(['y' => [3, 4]], $modifierX->beforeMerge($array, $allArrays));
        $this->assertSame(['x' => [1, 2]], $modifierY->beforeMerge($array, $allArrays));
    }

    public function testWithoutKeysInAllArrays(): void
    {
        $array = ['x' => [1, 2], 'y' => [3, 4], 'z' => [5, 6]];
        $allArrays = [
            $array,
            ['x' => [5], 'y' => [6]],
        ];

        $modifier = new ReplaceValueWhole('z');

        $this->assertSame(['x' => [1, 2], 'y' => [3, 4], 'z' => [5, 6]], $modifier->beforeMerge($array, $allArrays));
    }
}
