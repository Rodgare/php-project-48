<?php

namespace Differ\Formatters\Json;

function json(array $tree)
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
        $count = count($currentValue);
        $index = 0;

        foreach ($currentValue as $key => $val) {
            $index += 1;
            $newKey = trim($key, " ");
            $lines[] = $index === $count ?
            "{$currentIndent}\"{$newKey}\": {$iter($val, $depth + 1)}" :
            "{$currentIndent}\"{$newKey}\": {$iter($val, $depth + 1)},";
        }
        $index = 0;
        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($tree, 1);
}

function toString($value)
{
    return str_replace("NULL", "null", str_replace("'", "\"", var_export($value, true)));
}
