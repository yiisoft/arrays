<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection;

use LogicException;

final class ArrayCollectionIsImmutableException extends LogicException
{
    public function __construct()
    {
        parent::__construct('ArrayCollection is immutable object.');
    }
}
