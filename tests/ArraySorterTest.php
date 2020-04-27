<?php

namespace Yiisoft\Arrays\Tests;

use InvalidArgumentException;
use stdClass;
use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArraySorter;

final class ArraySorterTest extends TestCase
{
    public function testMultisort(): void
    {
        // empty key
        $dataEmpty = [];
        ArraySorter::multisort($dataEmpty, '');
        $this->assertEquals([], $dataEmpty);

        // single key
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArraySorter::multisort($array, 'name');
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArraySorter::multisort($array, ['name', 'age']);
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 2], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[2]);

        // case-insensitive
        $array = [
            ['name' => 'a', 'age' => 3],
            ['name' => 'b', 'age' => 2],
            ['name' => 'B', 'age' => 4],
            ['name' => 'A', 'age' => 1],
        ];

        ArraySorter::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[1]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[2]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[3]);

        ArraySorter::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING | SORT_FLAG_CASE, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[2]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[3]);

        ArraySorter::multisort($array, fn ($item) => ['age', 'name'], SORT_DESC);

        $this->assertEquals(['name' => 'B', 'age' => 4], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[2]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[3]);
    }

    public function testMultisortNestedObjects(): void
    {
        $obj1 = new stdClass();
        $obj1->type = 'def';
        $obj1->owner = $obj1;

        $obj2 = new stdClass();
        $obj2->type = 'abc';
        $obj2->owner = $obj2;

        $obj3 = new stdClass();
        $obj3->type = 'abc';
        $obj3->owner = $obj3;

        $models = [
            $obj1,
            $obj2,
            $obj3,
        ];

        $this->assertEquals($obj2, $obj3);

        ArraySorter::multisort($models, 'type', SORT_ASC);
        $this->assertEquals($obj2, $models[0]);
        $this->assertEquals($obj3, $models[1]);
        $this->assertEquals($obj1, $models[2]);

        ArraySorter::multisort($models, 'type', SORT_DESC);
        $this->assertEquals($obj1, $models[0]);
        $this->assertEquals($obj2, $models[1]);
        $this->assertEquals($obj3, $models[2]);
    }

    public function testMultisortUseSort(): void
    {
        // single key
        $orders = [
            'name' => SORT_ASC,
        ];

        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArraySorter::multisort($array, array_keys($orders), array_values($orders));
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $orders = [
            'name' => SORT_ASC,
            'age' => SORT_DESC,
        ];

        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArraySorter::multisort($array, array_keys($orders), array_values($orders));
        $this->assertEquals(['name' => 'a', 'age' => 2], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[2]);
    }

    public function testMultisortClosure(): void
    {
        $changelog = [
            '- Enh #123: test1',
            '- Bug #125: test2',
            '- Bug #123: test2',
            '- Enh: test3',
            '- Bug: test4',
        ];
        $i = 0;
        ArraySorter::multisort(
            $changelog,
            static function ($line) use (&$i) {
                if (preg_match('/^- (Enh|Bug)( #\d+)?: .+$/', $line, $m)) {
                    $o = ['Bug' => 'C', 'Enh' => 'D'];
                    return $o[$m[1]] . ' ' . (!empty($m[2]) ? $m[2] : 'AAAA' . $i++);
                }

                return 'B' . $i++;
            },
            SORT_ASC,
            SORT_NATURAL
        );
        $this->assertEquals(
            [
                '- Bug #123: test2',
                '- Bug #125: test2',
                '- Bug: test4',
                '- Enh #123: test1',
                '- Enh: test3',
            ],
            $changelog
        );
    }

    public function testMultisortInvalidArgumentExceptionDirection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArraySorter::multisort($data, ['foo'], []);
    }

    public function testMultisortInvalidArgumentExceptionSortFlag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArraySorter::multisort($data, ['foo'], ['foo'], []);
    }
}
