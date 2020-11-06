<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Collection\Modifier;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\Collection\ArrayCollection;
use Yiisoft\Arrays\Collection\Modifier\ReverseValues;

final class ReverseValuesTest extends TestCase
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
            ],
            new ReverseValues()
        );

        $this->assertSame(
            [
                'options' => [],
                'version' => '3.0',
                'name' => 'Yii',
            ],
            ArrayHelper::merge($a, $b)
        );
    }
}
