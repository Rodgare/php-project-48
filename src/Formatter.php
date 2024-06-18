<?php

namespace Differ\Formatter;

use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;

function changeFormat($tree, $format)
{
    return $format === 'plain' ? plain($tree) : stylish($tree);
}
