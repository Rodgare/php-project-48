<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use Differ\Formatter;

function genDiff(string $firstFilePath, string $secondFilePath)
{
    if (empty($firstFilePath) || empty($secondFilePath)) {
        return '';
    }
    if (!is_file($firstFilePath) || !is_file($secondFilePath)) {
        return 'Wrong path';
    }
    return decoder($firstFilePath, $secondFilePath);
}

function decoder(string $firstFilePath, string $secondFilePath)
{
    $extension = pathinfo($firstFilePath, PATHINFO_EXTENSION);
    return match ($extension) {
        'json' => combine(
            json_decode(file_get_contents($firstFilePath), true),
            json_decode(file_get_contents($secondFilePath), true)
        ),
        'yml', 'yaml' => implode("\n", array_map(
            fn($item) => "  $item",
            combine(
                Yaml::parse(file_get_contents($firstFilePath)),
                Yaml::parse(file_get_contents($secondFilePath))
            )
        )
        ),
    };
}

function combine(array $file1, array $file2): array
{
    $sorted = arrayMergeRecursiveThree(
        arrayDiffAssocRecursive($file1, $file2, "-"),
        arrayDiffAssocRecursive($file2, $file1, "+"),
        arrayIntersectAssocRecursive($file1, $file2)
    );

    return $sorted;
    /*return array_map(function ($item) {
        [$key, $val, $sign] = $item;
        return empty($sign) ? "  $key: $val" : "$sign $key: $val";
    }, $sorted);*/
}

function arrayDiffAssocRecursive($array1, $array2, $sign)
{
    $difference = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key] = [$value, $sign];
            } else {
                $new_diff = arrayDiffAssocRecursive($value, $array2[$key], $sign);
                if (!empty($new_diff)) {
                    $difference[$key] = $new_diff;
                }
            }
        } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = [$value, $sign];
        }
    }

    return $difference;
}

function arrayIntersectAssocRecursive($array1, $array2)
{
    $result = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value) && isset($array2[$key]) && is_array($array2[$key])) {
            $result[$key] = arrayIntersectAssocRecursive($value, $array2[$key]);
        } elseif (isset($array2[$key]) && $array2[$key] === $value) {
            $result[$key] = $value;
        }
    }

    return $result;
}

function arrayMergeRecursiveThree($array1, $array2, $array3)
{
    $arrays = [$array2, $array3];
    
    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            if (isset($array1[$key]) && is_array($array1[$key]) && is_array($value)) {
                $array1[$key] = arrayMergeRecursiveThree($array1[$key], $value, []);
            } else {
                $array1[$key] = $value;
            }
        }
    }

    return $array1;
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
