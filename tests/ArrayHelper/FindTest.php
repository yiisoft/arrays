<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use Closure;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class FindTest extends TestCase
{
    private array $array = [
        [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
            'e' => 5,
        ],
        [
            1, 2, 3, 4, 5,
        ],
    ];

    public function dataProviderFindFromArray(): array
    {
        return [
            [$this->array[0], fn ($value) => $value > 3, 4],
            [$this->array[1], fn ($value) => $value > 3, 4],
            [$this->array[1], fn ($value) => $value > 5, null],
            [$this->array[0], fn ($value, $key) => $key === 'c', 3],
            [$this->array[0], fn () => false, null],
            [[], fn () => true, null],
        ];
    }

    /**
     * @dataProvider dataProviderFindFromArray
     *
     * @param Closure $predicate
     * @param $expected
     */
    public function testFind($array, $predicate, $expected): void
    {
        $this->assertEquals($expected, ArrayHelper::find($array, $predicate));
    }

    public function dataProviderFindKeyFromArray(): array
    {
        return [
            [$this->array[0], fn ($value) => $value > 3, 'd'],
            [$this->array[1], fn ($value) => $value > 3, 3],
            [$this->array[1], fn ($value) => $value > 5, null],
            [$this->array[0], fn ($value, $key) => $key === 'c', 'c'],
            [$this->array[0], fn () => false, null],
            [[], fn () => true, null],
        ];
    }

    /**
     * @dataProvider dataProviderFindKeyFromArray
     *
     * @param Closure $predicate
     * @param $expected
     */
    public function testFindKey($array, $predicate, $expected): void
    {
        $this->assertEquals($expected, ArrayHelper::findKey($array, $predicate));
    }

    public function dataProviderAnyFromArray(): array
    {
        return [
            [$this->array[0], fn ($value) => $value > 3, true],
            [$this->array[1], fn ($value) => $value > 3, true],
            [$this->array[1], fn ($value) => $value > 5, false],
            [$this->array[0], fn ($value, $key) => $key === 'c', true],
            [$this->array[0], fn () => false, false],
            [[], fn () => true, false],
        ];
    }

    /**
     * @dataProvider dataProviderAnyFromArray
     *
     * @param Closure $predicate
     * @param $expected
     */
    public function testAny($array, $predicate, $expected): void
    {
        $this->assertEquals($expected, ArrayHelper::any($array, $predicate));
    }

    public function dataProviderAllFromArray(): array
    {
        return [
            [$this->array[0], fn ($value) => $value > 0, true],
            [$this->array[1], fn ($value) => $value > 0, true],
            [$this->array[1], fn ($value) => $value > 1, false],
            [[], fn () => true, true],
        ];
    }

    /**
     * @dataProvider dataProviderAllFromArray
     *
     * @param Closure $predicate
     * @param $expected
     */
    public function testAll($array, $predicate, $expected): void
    {
        $this->assertEquals($expected, ArrayHelper::all($array, $predicate));
    }
}
