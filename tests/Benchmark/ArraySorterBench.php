<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Benchmark;

use Yiisoft\Arrays\ArraySorter;

use const SORT_ASC;
use const SORT_DESC;
use const SORT_FLAG_CASE;
use const SORT_NUMERIC;
use const SORT_STRING;

/**
 * @BeforeMethods({"init"})
 */
final class ArraySorterBench
{
    private array $testArray = [];
    private array $testComplexArray = [];

    public function init(): void
    {
        // Prepare test data
        $this->testArray = [
            ['age' => 30, 'name' => 'Alexander'],
            ['age' => 30, 'name' => 'Brian'],
            ['age' => 19, 'name' => 'Barney'],
        ];

        $this->testComplexArray = [
            ['category' => 'electronics', 'price' => 100, 'name' => 'Phone'],
            ['category' => 'books', 'price' => 20, 'name' => 'PHP Guide'],
            ['category' => 'electronics', 'price' => 200, 'name' => 'Laptop'],
            ['category' => 'books', 'price' => 15, 'name' => 'Python Guide'],
        ];
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMultisortSingleKey(): void
    {
        $data = $this->testArray;
        ArraySorter::multisort($data, 'age');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMultisortMultipleKeys(): void
    {
        $data = $this->testArray;
        ArraySorter::multisort($data, ['age', 'name']);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMultisortWithDirections(): void
    {
        $data = $this->testComplexArray;
        ArraySorter::multisort(
            $data,
            ['category', 'price'],
            [SORT_ASC, SORT_DESC],
        );
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMultisortWithClosureAndFlags(): void
    {
        $data = $this->testComplexArray;
        ArraySorter::multisort(
            $data,
            [
                static fn($item) => strtolower($item['category']),
                'price',
            ],
            [SORT_ASC, SORT_DESC],
            [SORT_STRING | SORT_FLAG_CASE, SORT_NUMERIC],
        );
    }
}
