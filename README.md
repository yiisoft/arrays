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

The package provides:

- `ArrayHelper` that has static methods to work with arrays;
- `ArraySorter` that has static methods for sort arrays;
- `ArrayAccessTrait` provides the implementation for
[\IteratorAggregate](https://www.php.net/manual/class.iteratoraggregate),
[\ArrayAccess](https://www.php.net/manual/class.arrayaccess) and
[\Countable](https://www.php.net/manualn/class.countable.php);
- `ArrayableInterface` and `ArrayableTrait` for use in classes who want to support customizable representation of their instances.

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
- applyModifiers

### Other

- keyExists

## ArraySorter usage

Array sorter has one static method which usage is like the following:

```php
ArraySorter::multisort($data, ['age', 'name'], [SORT_ASC, SORT_DESC]);
```

## ArrayAccessTrait usage

...

## ArrayableInterface and ArrayableTrait usage

...
