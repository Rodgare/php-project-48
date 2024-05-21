<?php

namespace Differ\Parsers;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use Symfony\Component\Yaml\Yaml;

function decoder($file1, $file2)
{
    return "Parser\decoder works";
}