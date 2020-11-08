<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Arrays</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/arrays/v/stable.png)](https://packagist.org/packages/yiisoft/arrays)
[![Total Downloads](https://poser.pugx.org/yiisoft/arrays/downloads.png)](https://packagist.org/packages/yiisoft/arrays)
[![Build status](https://github.com/yiisoft/arrays/workflows/build/badge.svg)](https://github.com/yiisoft/arrays/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/arrays/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/arrays/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/arrays/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/arrays/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Farrays%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/arrays/master)
[![static analysis](https://github.com/yiisoft/arrays/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/arrays/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/arrays/coverage.svg)](https://shepherd.dev/github/yiisoft/arrays)

The package provides:

- `ArrayHelper` that has static methods to work with arrays;
- `ArraySorter` that has static methods for sort arrays;
- `ArrayAccessTrait` provides the implementation for
[\IteratorAggregate](https://www.php.net/manual/class.iteratoraggregate),
[\ArrayAccess](https://www.php.net/manual/class.arrayaccess) and
[\Countable](https://www.php.net/manualn/class.countable.php);
- `ArrayableInterface` and `ArrayableTrait` for use in classes who want to support customizable representation of their instances.
- `ArrayCollection` that allow to apply modifiers to arrays.

## Requirements

- PHP 7.4 or higher.

## Installation

```
composer require yiisoft/arrays
```

## ArrayHelper usage

Array helper methods are static so usage is like the following:

```php
$username = ArrayHelper::getValue($_POST, 'username');
```

Overall the helper has the following method groups.

### Getting data

- getValue
- getValueByPath
- getColumn
- getObjectVars

### Setting data

- setValue
- setValueByPath

### Removing data

- remove
- removeByPath
- removeValue

### Detecting array types

- isIndexed
- isAssociative

### HTML encoding and decoding values

- htmlDecode
- htmlEncode

### Testing against arrays

- isIn
- isSubset

### Transformation

- index
- filter
- map
- merge
- toArray

### Other

- keyExists

## ArraySorter usage

Array sorter has one static method which usage is like the following:

```php
ArraySorter::multisort($data, ['age', 'name'], [SORT_ASC, SORT_DESC]);
```

## ArrayAccessTrait usage

`ArrayAccessTrait` provides the implementation for
[\IteratorAggregate](https://www.php.net/manual/class.iteratoraggregate),
[\ArrayAccess](https://www.php.net/manual/class.arrayaccess) and
[\Countable](https://www.php.net/manualn/class.countable.php).
 
Note that `ArrayAccessTrait` requires the class using it contain a property named `data` which should be an array.
The data will be exposed by ArrayAccessTrait to support accessing the class object like an array.

Example of use:

```php
class OfficeClassification implements \IteratorAggregate, \ArrayAccess, \Countable
{
    use ArrayAccessTrait;

    public array $data = [
        'a' => 'Class A',
        'b' => 'Class B',
        'c' => 'Class C',
    ];
}

$classification = new OfficeClassification();

echo 'Count classes: ' . $classification->count() . "\n"; // 3

$iterator = $classification->getIterator();
while ($iterator->valid()) {
    echo $iterator->current() . "\n"; // Class A, Class B, Class C
    $iterator->next();
}
```

## ArrayableInterface and ArrayableTrait usage

`ArrayableInterface` and its implementation `ArrayableTrait` intended for use in classes who want to support customizable representation of their instances.

Example of use:

```php
class Car implements ArrayableInterface
{
    use ArrayableTrait;

    public string $type = 'Crossover';
    public string $color = 'Red';
    public int $torque = 472;
}

$car = new Car();

$data = $car->toArray(['type', 'color']); // ['type' => 'Crossover', 'color' => 'Red']
```

## ArrayCollection usage

`ArrayCollection` is array wrapper that allows specifying modifiers. When you get array value or whole array
from the collection modifiers are applied first so you get modified data.

When merging collections using `ArrayHelper::merge()` or `$collection->mergeWith()` original arrays
and modifers are merged separately.

Example of use:

```php
$collection = new ArrayCollection(
    [
        'name' => 'Yii',
        'version' => '2.0',
        'tag' => 'php',
    ],
    new SaveOrder(),
);

$update = new ArrayCollection(
    [
        'description' => 'PHP framework',
        'version' => '3.0',
    ],
    new UnsetValue('tag'),
    new MoveValueBeforeKey('description', 'version')
);

$update2 = [
    'license' => 'BSD',
];

// [
//    'name' => 'Yii',
//    'description' => 'PHP framework',
//    'version' => '3.0',
//    'license' => 'BSD',
// ]
$result = ArrayHelper::merge($collection, $update, $update2);
```

### Modifiers

Modifiers are specified as extra constructor elements of `ArrayCollection`:

```php
$dataSet = new ArrayCollection($data, new RemoveAllKeys(), new ReverseValues());
```

#### MoveValueBeforeKey

```php
new MoveValueBeforeKey('key', 'beforeKey');
```

Move element with a key `key` before an element with `beforeKey` key.

#### RemoveAllKeys

```php
new RemoveAllKeys();
```

Re-indexes an array numerically, i. e. removes all information about array keys.

#### ReplaceValue

```php
new ReplaceValue('key');
```

The modifier allows to mark an array element from the collection it is applied to,
as the element to be processed in a special way on merge.
 
- In case there are elements with the same keys in previous arrays, they will be replaced
  with a value from the current array.
- If there are elements with the same keys in next arrays, they will replace current array value.

If there is no element with the given key in the array, modifier won't change anything.

Note that this modifier is applied on merge.

#### ReverseValues

```php
new ReverseValues();
```

Reverse order of an array elements.

#### SaveOrder

```php
new SaveOrder();
```

Remembers the order of elements in the collection it is applied to and tried to keep the order while merging.

#### UnsetValue

```php
new UnsetValue('key');
```

Removes an array element with a given key.

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```
./vendor/bin/psalm
```

## License

The Yii Arrays is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
