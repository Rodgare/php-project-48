<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatter\changeFormat;
use function Differ\InnerDiff\genInnerDiff;

function genDiff(mixed $firstFilePath, mixed $secondFilePath, string $format = 'stylish')
{
    $result = match (pathinfo($firstFilePath, PATHINFO_EXTENSION)) {
        'yml', 'yaml' => genInnerDiff(
            Yaml::parse(falseToString(file_get_contents($firstFilePath))),
            Yaml::parse(falseToString(file_get_contents($secondFilePath)))
        ),
        default => genInnerDiff(
            json_decode(falseToString(file_get_contents($firstFilePath)), true),
            json_decode(falseToString(file_get_contents($secondFilePath)), true)
        )
    };

    return changeFormat($result, $format);
}

function falseToString(mixed $str): string
{
    return $str === false ? '' : $str;
}
