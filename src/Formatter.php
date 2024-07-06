<?php

namespace Differ\Formatter;

use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Json\json;

function changeFormat(array $tree, string $format): string
{
    return match ($format) {
        'plain' => plain($tree),
        'json' => json($tree),
        default => stylish($tree),
    };
}
