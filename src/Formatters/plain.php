<?php

namespace Differ\Formatters\Plain;

function normalize(mixed $value)
{
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_string($value)) {
        return "'$value'";
    }
    if ($value === true) {
        return 'true';
    }
    if ($value === false) {
        return 'false';
    }
    if ($value === null) {
        return 'null';
    }
    return $value;
}

function iter(array $diff, array $path)
{
    $keys = array_keys($diff);

    $result = array_map(function ($key) use ($diff, $path) {
        $status = $diff[$key]['status'];
        $values = $diff[$key]['values'];
        $newPath = [...$path, $key];
        $fullKey = implode('.', $newPath);

        if ($status === 'removed') {
            $str = "Property '$fullKey' was removed";
        } elseif ($status === 'added') {
            $value = $values['value'];
            $val = normalize($value);
            $str = "Property '$fullKey' was added with value: $val";
        } elseif ($status === 'updated') {
            if (array_key_exists('diff', $values)) {
                $str = iter($values['diff'], $newPath);
            } else {
                $oldVal = normalize($values['oldValue']);
                $newVal = normalize($values['newValue']);
                $str = "Property '$fullKey' was updated. From $oldVal to $newVal";
            }
        } else {
            $str = '';
        }

        return $str;
    }, $keys);

    $filteredResult = array_filter($result, fn($str) => $str !== '');
    return implode("\n", $filteredResult);
}

function plain(array $diff)
{
    return iter($diff, []);
}
