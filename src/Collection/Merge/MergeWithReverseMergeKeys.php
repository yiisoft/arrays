<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Merge;

use InvalidArgumentException;
use Yiisoft\Arrays\Collection\ArrayCollection;

final class MergeWithReverseMergeKeys implements MergeStrategy
{


    private static function isMergable($value): bool
    {
        return is_array($value) || $value instanceof ArrayCollection;
    }
}
