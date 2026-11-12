<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Tests;

use Yiisoft\Arrays\ArrayableTrait;
use Yiisoft\Arrays\ArrayAccessTrait;

/**
 * @internal Need for correct static analysis.
 */
final class PhpStanTraitHelper
{
    use ArrayableTrait;

    /** @use ArrayAccessTrait<array-key, mixed> */
    use ArrayAccessTrait;

    /** @var array<array-key, mixed> */
    public array $data = [];
}
