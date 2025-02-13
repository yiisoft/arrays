<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests\Benchmark;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayAccessTrait;

/**
 * @BeforeMethods({"init"})
 */
final class ArrayHelperBench
{
    private array $testArray = [];
    private array $testNestedArray = [];
    private array $testObjectArray = [];
    private array $testLargeArray = [];
    private TestArrayable $testArrayable;
    private TestArrayAccess $testArrayAccess;

    public function init(): void
    {
        // Prepare test data
        $this->testArray = [
            ['id' => 1, 'name' => 'John', 'age' => 30],
            ['id' => 2, 'name' => 'Jane', 'age' => 25],
            ['id' => 3, 'name' => 'Bob', 'age' => 35],
        ];

        $this->testNestedArray = [
            'user' => [
                'profile' => [
                    'name' => 'John',
                    'age' => 30,
                ],
            ],
        ];

        $this->testObjectArray = [
            (object)['id' => 1, 'name' => 'John'],
            (object)['id' => 2, 'name' => 'Jane'],
        ];

        // Prepare larger array for more complex operations
        $this->testLargeArray = [];
        for ($i = 0; $i < 100; $i++) {
            $this->testLargeArray[] = [
                'id' => $i,
                'category' => $i % 3 === 0 ? 'A' : ($i % 3 === 1 ? 'B' : 'C'),
                'value' => $i * 10,
                'name' => 'Item ' . $i,
                'html' => '<p>Item ' . $i . '</p>',
            ];
        }

        $this->testArrayable = new TestArrayable();
        $this->testArrayAccess = new TestArrayAccess();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGetValue(): void
    {
        ArrayHelper::getValue($this->testArray, '0.name');
        ArrayHelper::getValue($this->testArray, '1.age');
        ArrayHelper::getValue($this->testArray, '2.id');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGetValueByPath(): void
    {
        ArrayHelper::getValueByPath($this->testNestedArray, 'user.profile.name');
        ArrayHelper::getValueByPath($this->testNestedArray, 'user.profile.age');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIndex(): void
    {
        ArrayHelper::index($this->testArray, 'id');
        ArrayHelper::index($this->testArray, 'name');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchToArray(): void
    {
        ArrayHelper::toArray($this->testObjectArray);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchRemove(): void
    {
        $array = $this->testArray;
        ArrayHelper::remove($array, '0');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchHtmlDecode(): void
    {
        $data = [
            'name' => 'John &amp; Jane',
            'description' => 'This &lt;b&gt;is&lt;/b&gt; a test',
        ];
        ArrayHelper::htmlDecode($data);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMerge(): void
    {
        $array1 = ['id' => 1, 'name' => 'John'];
        $array2 = ['age' => 30, 'city' => 'New York'];
        ArrayHelper::merge($array1, $array2);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchFilter(): void
    {
        ArrayHelper::filter($this->testLargeArray, ['category' => 'A']);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGroup(): void
    {
        ArrayHelper::group($this->testLargeArray, 'category');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMap(): void
    {
        ArrayHelper::map($this->testLargeArray, 'id', 'name');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchRemoveByPath(): void
    {
        $array = $this->testNestedArray;
        ArrayHelper::removeByPath($array, 'user.profile.name');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSetValue(): void
    {
        $array = $this->testNestedArray;
        ArrayHelper::setValue($array, 'user.profile.name', 'Jane');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchHtmlEncode(): void
    {
        ArrayHelper::htmlEncode($this->testLargeArray);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIsAssociative(): void
    {
        ArrayHelper::isAssociative($this->testArray);
        ArrayHelper::isAssociative($this->testLargeArray);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIsIndexed(): void
    {
        ArrayHelper::isIndexed([1, 2, 3]);
        ArrayHelper::isIndexed($this->testArray);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIsIn(): void
    {
        ArrayHelper::isIn('John', array_column($this->testArray, 'name'));
        ArrayHelper::isIn('Unknown', array_column($this->testArray, 'name'));
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchKeyExists(): void
    {
        $array = ['name' => 'John'];
        ArrayHelper::keyExists($array, 'name');
        ArrayHelper::keyExists($array, 'unknown');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchPathExists(): void
    {
        ArrayHelper::pathExists($this->testNestedArray, 'user.profile.name');
        ArrayHelper::pathExists($this->testNestedArray, 'user.profile.unknown');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchRenameKey(): void
    {
        $array = $this->testArray[0];
        ArrayHelper::renameKey($array, 'name', 'fullName');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchAddValue(): void
    {
        $array = $this->testArray;
        ArrayHelper::addValue($array, 'status', 'active');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchParametrizedMerge(): void
    {
        $array1 = ['id' => 1, 'name' => ['first' => 'John']];
        $array2 = ['age' => 30, 'name' => ['last' => 'Doe']];
        ArrayHelper::parametrizedMerge([$array1, $array2], 2);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchAddValueByPath(): void
    {
        $array = $this->testNestedArray;
        ArrayHelper::addValueByPath($array, 'user.profile.email', 'john@example.com');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSetValueByPath(): void
    {
        $array = $this->testNestedArray;
        ArrayHelper::setValueByPath($array, 'user.profile.name', 'Jane');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchRemoveValue(): void
    {
        $array = $this->testArray;
        ArrayHelper::removeValue($array, ['id' => 1, 'name' => 'John', 'age' => 30]);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGetColumn(): void
    {
        ArrayHelper::getColumn($this->testArray, 'name');
        ArrayHelper::getColumn($this->testArray, 'age', true);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIsSubset(): void
    {
        $needles = [['id' => 1, 'name' => 'John']];
        ArrayHelper::isSubset($needles, $this->testArray);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGetObjectVars(): void
    {
        ArrayHelper::getObjectVars($this->testObjectArray[0]);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayableFields(): void
    {
        $this->testArrayable->fields();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayableExtraFields(): void
    {
        $this->testArrayable->extraFields();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayableToArray(): void
    {
        $this->testArrayable->toArray(['name', 'email'], ['profile']);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessGetIterator(): void
    {
        $this->testArrayAccess->getIterator();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessCount(): void
    {
        $this->testArrayAccess->count();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessOffsetExists(): void
    {
        $this->testArrayAccess->offsetExists('key1');
        $this->testArrayAccess->offsetExists('nonexistent');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessOffsetGet(): void
    {
        $this->testArrayAccess->offsetGet('key1');
        $this->testArrayAccess->offsetGet('key2');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessOffsetSet(): void
    {
        $this->testArrayAccess->offsetSet('key3', 'value3');
        $this->testArrayAccess->offsetSet(null, 'value4');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchArrayAccessOffsetUnset(): void
    {
        $this->testArrayAccess->offsetUnset('key1');
    }
}

/**
 * Test class implementing ArrayableInterface
 */
final class TestArrayable implements ArrayableInterface
{
    private string $name = 'Test';
    private string $email = 'test@example.com';
    private array $profile = ['age' => 30];

    public function fields(): array
    {
        return ['name', 'email'];
    }

    public function extraFields(): array
    {
        return ['profile'];
    }

    public function toArray(array $fields = [], array $expand = [], bool $recursive = true): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (in_array('profile', $expand, true)) {
            $data['profile'] = $this->profile;
        }

        return $data;
    }
}

/**
 * Test class implementing ArrayAccess
 */
final class TestArrayAccess
{
    use ArrayAccessTrait;

    public function __construct()
    {
        $this->data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
    }
}
