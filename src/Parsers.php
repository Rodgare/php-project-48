<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function decoder(string $firstFilePath, string $secondFilePath): string
{
    $extension = pathinfo($firstFilePath, PATHINFO_EXTENSION);
    return match ($extension) {
        'json' => implode("\n", array_map(fn($item) => "  $item", combine(
            json_decode(file_get_contents($firstFilePath), true),
            json_decode(file_get_contents($secondFilePath), true)
        ))),
        'yml', 'yaml' => implode("\n", array_map(fn($item) => "  $item", combine(
            Yaml::parse(file_get_contents($firstFilePath)),
            Yaml::parse(file_get_contents($secondFilePath))
        ))),
    };
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
