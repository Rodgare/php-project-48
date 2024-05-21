<?php

namespace Differ\Differ;

use function Differ\Parsers\decoder;

function genDiff(string $firstFilePath, string $secondFilePath): string
{
    if (empty($firstFilePath) || empty($secondFilePath)) {
        return '';
    }
    if (!is_file($firstFilePath) || !is_file($secondFilePath)) {
        return 'Wrong path';
    }
    $result = decoder($firstFilePath, $secondFilePath);
    return "\n{\n" . $result . "\n}\n";
}
