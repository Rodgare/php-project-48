<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use function Differ\Differ\combine;

function decoder($firstFilePath, $secondFilePath)
{
    $file1 = json_decode(file_get_contents($firstFilePath), true);
    $file2 = json_decode(file_get_contents($secondFilePath), true);
    $result = implode("\n", array_map(fn($item) => "  $item", combine($file1, $file2)));
    
    return $result;
}