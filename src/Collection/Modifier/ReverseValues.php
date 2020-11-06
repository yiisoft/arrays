<?php

declare(strict_types=1);

namespace Yiisoft\Arrays\Collection\Modifier;

use Yiisoft\Arrays\Collection\Modifier\ModifierInterface\DataModifierInterface;

/**
 * Modifier "Reverse Values".
 *
 * При преобразовании коллекции в массив порядок элементов будет изменён на противоположный.
 */
final class ReverseValues implements DataModifierInterface
{
    public function apply(array $data): array
    {
        return array_reverse($data);
    }
}
