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
        'yml', 'yaml' => implode(
            "\n",
            array_map(
                fn($item) => "  $item",
                combine(
                    Yaml::parse(file_get_contents($firstFilePath)),
                    Yaml::parse(file_get_contents($secondFilePath))
                )
            )
        ),
    };
}

function combine(array $file1, array $file2)
{
    $sorted = sorting(
        arrayMerge(
            arrayDiffAssocRecursive($file1, $file2, '8'),
            arrayDiffAssocRecursive($file2, $file1, '9'),
            arrayIntersectAssocRecursive($file1, $file2, ' ')
        )
    );

    return $sorted;
}

function arrayDiffAssocRecursive($array1, $array2, $sign)
{
    $difference = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key . $sign] = $value;
            } else {
                $new_diff = arrayDiffAssocRecursive($value, $array2[$key], $sign);
                if (!empty($new_diff)) {
                    $difference[$key . ' '] = $new_diff;
                }
            }
        } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key . $sign] = $value;
        }
    }

    return $difference;
}

function arrayIntersectAssocRecursive($array1, $array2, $sign)
{
    $result = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value) && isset($array2[$key]) && is_array($array2[$key])) {
            $result[$key . $sign] = arrayIntersectAssocRecursive($value, $array2[$key], $sign);
        } elseif (isset($array2[$key]) && $array2[$key] === $value) {
            $result[$key . $sign] = $value;
        }
    }

    return $result;
}

function arrayMerge($arr1, $arr2, $arr3)
{
    $arrays = func_get_args();
    $merged = array();

    foreach ($arrays as $array) {
        foreach ($array as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
    }

    return $merged;
}



function sorting(array $arr): array
{
    ksort($arr);

    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $arr[$key] = sorting($val);
        }
    }

    return $arr;
}
