<?php

namespace Differ\Formatter;

function stylish(array $tree): string
{
    $replacer = ' ';
    $spacesCount = 2;
    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spacesCount) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $spacesCount;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);

        $lines = array_map(
            fn($key, $val) => "{$currentIndent}{$key}: {$iter($val, $depth + 2)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($tree, 1);
}

function plain($tree, $path = '')
{
    $result = array_map(
        function ($key, $val) use ($path) {
            $Newkey = normalizeKey($key);
            $path .= $Newkey;
            if (str_contains($key, '+')) {
                $newValue = is_array($val) ? "[complex value]" : normalizeString($val);
                return "Property '$path' was added with value: $newValue";
            } elseif (str_contains($key, '-')) {
                return "Property '$path' was removed";
            } elseif (str_contains($key, '=')) {
                $oldValue = is_array($val['updatedFrom']) ? "[complex value]" : normalizeString($val['updatedFrom']);
                $newValue = is_array($val['updatedTo']) ? "[complex value]" : normalizeString($val['updatedTo']);
                return "Property '$path' was updated. From $oldValue to $newValue";
            }
            if (is_array($val) && !array_key_exists("updatedFrom", $val)) {
                return plain($val, $path . '.');
            }
        },
        array_keys(updatedItems($tree)),
        updatedItems($tree)
    );

    return implode("\n", array_filter(flattenAll($result)));
}

function updatedItems($tree)
{
    $oldKeys = [];
    foreach ($tree as $key => $val) {
        $trimKey = '- ' . trim($key, " +");
        if (array_key_exists($trimKey, $oldKeys)) {
            is_array($val) ?
                $oldKeys[$trimKey] = updatedItems($val) :
                $oldKeys['=' . trim($key, "+")] = (['updatedFrom' => $tree[$trimKey], 'updatedTo' => $val]);
            unset($oldKeys[$trimKey]);
        } else {
            is_array($val) ?
                $oldKeys[$key] = updatedItems($val) :
                $oldKeys[$key] = $val;
        }
    }

    return $oldKeys;
}

function flattenAll($collection)
{
    $result = [];

    foreach ($collection as $value) {
        if (is_array($value)) {
            $result = array_merge($result, flattenAll($value));
        } else {
            $result[] = $value;
        }
    }

    return $result;
}

function toString($value)
{
    return str_replace("NULL", "null", str_replace("'", "", var_export($value, true)));
}

function normalizeString($val)
{
    return str_replace("NULL", "null", trim(var_export($val, true), "+-="));
}

function normalizeKey($key)
{
    return trim($key, " =-+");
}
