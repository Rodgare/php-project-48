<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatter\changeFormat;

function genDiff(mixed $firstFilePath, mixed $secondFilePath, string $format = 'stylish')
{
    $result = match (pathinfo($firstFilePath, PATHINFO_EXTENSION)) {
        'yml', 'yaml' => combine(
            Yaml::parse(falseToString(file_get_contents($firstFilePath))),
            Yaml::parse(falseToString(file_get_contents($secondFilePath)))
        ),
        default => combine(
            json_decode(falseToString(file_get_contents($firstFilePath)), true),
            json_decode(falseToString(file_get_contents($secondFilePath)), true)
        )
    };

    return changeFormat($result, $format);
}

function combine(array $file1, array $file2): array
{
    return addSign(
        sorting(
            arrayMerge(
                arrayDiff($file1, $file2, '8'),
                arrayDiff($file2, $file1, '9'),
                arrayIntersect($file1, $file2)
            )
        )
    );
}

function arrayDiff(array $array1, array $array2, string $sign): array
{
    return array_reduce(array_keys($array1), function ($result, $key) use ($array1, $array2, $sign) {
        $value = $array1[$key];
        $newKey = "{$key}{$sign}";
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $result[$newKey] = $value;
            } else {
                $new_diff = arrayDiff($value, $array2[$key], $sign);
                if (!empty($new_diff)) {
                    $result[$key] = $new_diff;
                }
            }
        } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $result[$newKey] = $value;
        }

        return $result;
    }, []);
}

function arrayIntersect(array $array1, array $array2): array
{
    $result = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value) && isset($array2[$key]) && is_array($array2[$key])) {
            $result[$key] = arrayIntersect($value, $array2[$key]);
        } elseif (isset($array2[$key]) && $array2[$key] === $value) {
            $result[$key] = $value;
        }
    }

    return $result;
}

function arrayMerge(array $arr1, array $arr2, array $arr3): array
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

function addSign(array $arr): array
{
    $result = [];
    foreach ($arr as $key => $val) {
        $newKey = $key;
        if (str_contains($key, '8')) {
            $newKey = '- ' . str_replace('8', '', $key);
        } elseif (str_contains($key, '9')) {
            $newKey = '+ ' . str_replace('9', '', $key);
        } elseif (!str_contains($key, '9') && !str_contains($key, '8')) {
            $newKey = '  ' . $key;
        }
        $result[$newKey] = $val;
        if (is_array($val)) {
            $result[$newKey] = addSign($val);
        }
    }

    return $result;
}

function falseToString(mixed $str): string
{
    return $str === false ? '' : $str;
}
