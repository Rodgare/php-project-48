<?php

namespace Differ\Formatters\Stylish;

function getPad(int $depth)
{
    $count = 4 * ($depth - 1);
    $padSmall = str_repeat(' ', $count);
    $pad = str_repeat(' ', $count + 2);
    return [$padSmall, $pad];
}

function prepare(mixed $value, int $depth)
{
    [$padSmall, $pad] = getPad($depth);
    if ($value === true) {
        return ' true';
    }
    if ($value === false) {
        return ' false';
    }
    if ($value === null) {
        return ' null';
    }
    if ($value === 0) {
        return ' 0';
    }
    if ($value === '') {
        return ' ';
    }
    if (!is_array($value)) {
        return " $value";
    }

    $keys = array_keys($value);
    $result = array_map(function ($key) use ($value, $pad, $depth) {
        $val = prepare($value[$key], $depth + 1);
        return "$pad  $key:$val";
    }, $keys);
    $innerPart = implode("\n", $result);
    return " {\n$innerPart\n$padSmall}";
}

function iter(array $diff, int $depth)
{
    $keys = array_keys($diff);
    [$padSmall, $pad] = getPad($depth);

    $result = array_map(function ($key) use ($diff, $pad, $depth) {
        $status = $diff[$key]['status'];
        $values = $diff[$key]['values'];

        if ($status === 'removed') {
            $val = prepare($values['value'], $depth + 1);
            $str = "$pad- $key:$val";
        } elseif ($status === 'added') {
            $val = prepare($values['value'], $depth + 1);
            $str = "$pad+ $key:$val";
        } elseif ($status === 'unchanged') {
            $val = prepare($values['value'], $depth + 1);
            $str = "$pad  $key:$val";
        } else {
            if (array_key_exists('diff', $values)) {
                $val = iter($values['diff'], $depth + 1);
                $str = "$pad  $key: $val";
            } else {
                $oldVal = prepare($values['oldValue'], $depth + 1);
                $newVal = prepare($values['newValue'], $depth + 1);
                $str = "$pad- $key:$oldVal\n$pad+ $key:$newVal";
            }
        }

        return $str;
    }, $keys);

    $innerPart = implode("\n", $result);
    return "{\n$innerPart\n$padSmall}";
}

function stylish(array $diff)
{
    return iter($diff, 1);
}
