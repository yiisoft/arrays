# ArrayHelper::filter

Filters array according to rules specified.

## Description

```php
ArrayHelper::filter(array $array, array $filters): array
```

Filters array **array** by rules **filters** specified.

## Parameters

**array**  
Source array.

**filters**   
Rules that define array keys which should be left or removed from results.  
Each rule is:
    
- `var`: `$array['var']` will be left in result.
- `var.key`: only `$array['var']['key']` will be left in result.
- `!var.key`: `$array['var']['key']` will be removed from result.


## Return Values

Filtered array.

## Examples

```php
$array = [
  'A' => [1, 2],
  'B' => [
      'C' => 1,
      'D' => 2,
  ],
  'E' => 1,
];

$result = ArrayHelper::filter($array, ['A']);
// $result will be:
// [
//     'A' => [1, 2],
// ]

$result = ArrayHelper::filter($array, ['A', 'B.C']);
// $result will be:
// [
//     'A' => [1, 2],
//     'B' => [
//         'C' => 1,
//     ],
// ]

$result = ArrayHelper::filter($array, ['B', '!B.C']);
// $result will be:
// [
//     'B' => [
//         'D' => 2,
//     ],
// ]
```
