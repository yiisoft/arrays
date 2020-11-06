<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\ReplaceValue;

final class ReplaceValueTest extends TestCase
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
                'options' => [
                    'unittest' => true,
                ],
                'features' => [
                    'gii',
                ],
            ],
            new ReplaceValue('features')
        );

        $this->assertEquals(
            [
                'name' => 'Yii',
                'version' => '3.0',
                'options' => [
                    'namespace' => false,
                    'unittest' => true,
                ],
                'features' => [
                    'gii',
                ],
            ],
            ArrayHelper::merge($a, $b)
        );
    }

    public function testForKey(): void
    {
        $modifierX = new ReplaceValue('x');
        $modifierY = $modifierX->forKey('y');

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifierX),
            ['x' => [5], 'y' => [6]],
        ];
        $this->assertSame(
            ['x' => [5], 'y' => [3, 4, 6]],
            ArrayHelper::merge(...$arrays)
        );

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifierY),
            ['x' => [5], 'y' => [6]],
        ];
        $this->assertSame(
            ['x' => [1, 2, 5], 'y' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithoutKeysInAllArrays(): void
    {
        $modifier = new ReplaceValue('z');

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4], 'z' => [5, 6]]))->withModifier($modifier),
            ['x' => [5], 'y' => [6]],
        ];

        $this->assertSame(
            ['x' => [1, 2, 5], 'y' => [3, 4, 6], 'z' => [5, 6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithKeysInArrayBeforeCurrent(): void
    {
        $modifier = new ReplaceValue('y');

        $arrays = [
            ['y' => [5]],
            (new ArrayCollection(['a' => 1, 'x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['z' => [6]],
        ];

        $this->assertSame(
            ['y' => [3, 4], 'a' => 1, 'x' => [1, 2], 'z' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithKeysInArrayBeforeAndAfterCurrent(): void
    {
        $modifier = new ReplaceValue('y');

        $arrays = [
            ['y' => [5]],
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['z' => [6], 'y' => [7]],
        ];

        $this->assertSame(
            ['y' => [7], 'x' => [1, 2], 'z' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testWithoutKeyInArray(): void
    {
        $modifier = new ReplaceValue('a');

        $arrays = [
            (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier),
            ['z' => [6]],
        ];

        $this->assertSame(
            ['x' => [1, 2], 'y' => [3, 4], 'z' => [6]],
            ArrayHelper::merge(...$arrays)
        );
    }

    public function testResetModifier(): void
    {
        $modifier = new ReplaceValue('y');

        $collection = (new ArrayCollection(['x' => [1, 2], 'y' => [3, 4]]))->withModifier($modifier);

        $arrays = [
            ['y' => [7]],
            $collection,
        ];
        ArrayHelper::merge(...$arrays);

        $this->assertSame(
            ['a' => 1],
            $collection->withData(['a' => 1])->mergeWith([])->toArray()
        );
    }
}
