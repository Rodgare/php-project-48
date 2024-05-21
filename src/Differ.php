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

function combine(array $file1, array $file2): array
{
    $sorted = sorting(
        array_merge(
            setSign(array_diff($file1, $file2), '-'),
            setSign(array_diff($file2, $file1), '+'),
            setSign(array_intersect($file1, $file2))
        )
    );

    return array_map(function ($item) {
        [$key, $val, $sign] = $item;
        return empty($sign) ? "  $key: $val" : "$sign $key: $val";
    }, $sorted);
}

function sorting(array $arr): array
{
    usort($arr, fn($a, $b) => $a[0] <=> $b[0]);

    return $arr;
}

function setSign(array $arr, string $sign = ''): array
{
    return array_map(function ($key, $val) use ($sign) {
        if ($val === true) {
            $val = 'true';
        } elseif ($val === false) {
            $val = 'false';
        }
        return [$key, $val, $sign];
    }, array_keys($arr), $arr);
}
