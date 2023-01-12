<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\ArrayHelper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;

final class KeyAndPathExistsTest extends TestCase
{
    private array $array = [
        'name' => 'test',
        'date' => '31-12-2113',
        'post' => [
            'id' => 5,
            'author' => [
                'name' => 'cebe',
                'profile' => [
                    'title' => '1337',
                ],
            ],
        ],
        'admin.firstname' => 'Qiang',
        'admin.lastname' => 'Xue',
        'admin' => [
            'lastname' => 'cebe',
        ],
        'version' => [
            '1.0' => [
                'status' => 'released',
                'name' => 'world',
            ],
            '1.0.status' => 'dev',
            2 => 'two',
        ],
        '42.7' => 500,
    ];

    /**
     * @return array[] common test data for [[testKeyExists()]] and [[testPathExists()]]
     */
    private function commonData(): array
    {
        return [
            [true, 'name'],
            [false, 'Name'],
            [true, 'Name', false],
            [true, ['version', 2]],
            [true, ['version', 2.0]],
            [true, 42.7],
            [false, 'noname'],
        ];
    }

    public function dataKeyExists(): array
    {
        return array_merge($this->commonData(), [
            [true, 'admin.firstname'],
            [true, 'admin.lastname'],
            [false, 'post.id'],
            [true, ['version', '1.0', 'status']],
            [false, ['version', '1.0', 'date']],
            [false, ['version', '1.0.name']],
            [false, ['post', 'author.name']],
            [true, '42.7'],
        ]);
    }

    /**
     * @dataProvider dataKeyExists
     */
    public function testKeyExists(bool $expected, mixed $key, bool $caseSensitive = true): void
    {
        $this->assertSame($expected, ArrayHelper::keyExists($this->array, $key, $caseSensitive));
    }

    public function dataPathExists(): array
    {
        return array_merge($this->commonData(), [
            [true, 'post.id'],
            [false, 'post.id.value'],
            [false, 'post.ID'],
            [true, 'post.ID', false],
            [false, 'post.id.value', false],
            [false, 'nopost.id'],
            [true, 'post.author.name'],
            [true, 'post.AUTHOR.name', false],
            [true, 'POST.author.NAME', false],
            [false, 'post.author.noname'],
            [false, 'post.author.noname', false],
            [false, 'admin.firstname'],
            [true, 'admin.lastname'],
            [false, 'version.1.0.status'],
            [true, 'version.2'],
            [false, ['version', '1.0', 'status']],
            [true, ['post', 'author.name']],
            [true, ['post', ['author', ['profile.title']]]],
            [false, '42.7'],
        ]);
    }

    /**
     * @dataProvider dataPathExists
     */
    public function testPathExist(bool $expected, mixed $key, bool $caseSensitive = true): void
    {
        $this->assertSame($expected, ArrayHelper::pathExists($this->array, $key, $caseSensitive));
    }
}
