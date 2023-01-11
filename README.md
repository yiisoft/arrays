<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
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

## Requirements

- PHP 8.0 or higher.

## Installation

```shell
composer require yiisoft/arrays --prefer-dist
```

## ArrayHelper usage

Array helper methods are static so usage is like the following:

```php
$username = \Yiisoft\Arrays\ArrayHelper::getValue($_POST, 'username');
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
- group
- filter
- map
- merge
- toArray

### Other

- keyExists
- pathExists

## ArraySorter usage

Array sorter has one static method which usage is like the following:

```php
\Yiisoft\Arrays\ArraySorter::multisort($data, ['age', 'name'], [SORT_ASC, SORT_DESC]);
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
use \Yiisoft\Arrays\ArrayAccessTrait;

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
use \Yiisoft\Arrays\ArrayableTrait;
use \Yiisoft\Arrays\ArrayableInterface;

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

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Arrays is free software. It is released under the terms of the BSD License. Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
