<?php

declare(strict_types=1);

namespace Yiisoft\Arrays;

/**
 * @internal Need for correct psalm analysis.
 * @see https://github.com/vimeo/psalm/issues/10920
 */
final class PsalmTraitHelper
{
    /** @use ArrayAccessTrait<array-key, mixed> */
    use ArrayAccessTrait;

    use ArrayableTrait;

    public array $data = [];
}
