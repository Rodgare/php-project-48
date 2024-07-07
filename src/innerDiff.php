<?php

namespace Differ\InnerDiff;

use Illuminate\Support\Collection;

function genInnerDiff(array $data1, array $data2)
{
    $keys1 = array_keys($data1);
    $keys2 = array_keys($data2);
    $unsortedKeys = array_unique(array_merge($keys1, $keys2));
    $keys = collect($unsortedKeys)->sort()->values()->all();

    $diff = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        $condition1 = array_key_exists($key, $data1);
        $condition2 = array_key_exists($key, $data2);
        $value1 = array_key_exists($key, $data1) ? $data1[$key] : 0;
        $value2 = array_key_exists($key, $data2) ? $data2[$key] : 0;

        if ($condition1 && !$condition2) {
            $status = 'removed';
            $values = ['value' => $value1];
        } elseif ($condition2 && !$condition1) {
            $status = 'added';
            $values = ['value' => $value2];
        } elseif ($value1 === $value2) {
            $status = 'unchanged';
            $values = ['value' => $value1];
        } elseif (!is_array($value1) || !is_array($value2)) {
            $status = 'updated';
            $values = ['oldValue' => $value1, 'newValue' => $value2];
        } else {
            $status = 'updated';
            $values = ['diff' => genInnerDiff($value1, $value2)];
        }

        return array_merge($acc, ["$key" => ['status' => $status, 'values' => $values]]);
    }, []);
    return $diff;
}
