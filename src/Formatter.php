<?php

namespace Differ\Formatter;

function toString($value)
{
    return str_replace("NULL", "null", str_replace("'", "", var_export($value, true)));
}

function normilizeString($val)
{
    $result = str_replace("NULL", "null", $val);
    return trim(var_export($result, true), " +-=");
}

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

function plain($tree)
{
    $result = array_map(
        function ($key, $val) {
            
        },
        array_keys(mergeUpdatedItems($tree)),
        mergeUpdatedItems($tree)
    );
    return $result;
}

function mergeUpdatedItems($tree)
{
    $oldKeys = [];
    foreach ($tree as $key => $val) {
        $trimKey = '- ' . trim($key, " +");
        if (array_key_exists($trimKey, $oldKeys)) {
            is_array($val) ?
                $oldKeys[$trimKey] = mergeUpdatedItems($val) :
                $oldKeys['=' . trim($key, "+")] = (['from' => $tree[$trimKey], 'to' => $val]);
                unset($oldKeys[$trimKey]);
        } else {
            is_array($val) ?
                $oldKeys[$key] = mergeUpdatedItems($val) :
                $oldKeys[$key] = $val;
        }
    }

    return $oldKeys;
}