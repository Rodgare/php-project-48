<?php

namespace Differ\Differ;

function setSign(array $arr, string $sign = '')
{
    $result = [];
    foreach ($arr as $key => $val) {
        if ($val === true) {
            $val = 'true';
        } elseif ($val === false) {
            $val = 'false';
        }
        $result[] = [$key, $val, $sign];
        //$result[] = "$sign $key: $val\n";
    }
    return $result;
}

function combine(array $json1, array $json2)
{
    $merged = array_merge(
        setSign(array_diff($json1, $json2), '-'),
        setSign(array_diff($json2, $json1), '+'), 
        setSign(array_intersect($json1, $json2))
    );
    usort($merged, fn($a, $b) => $a[0] <=> $b[0]);   
    
    return array_map(function ($item) {
        [$key, $val, $sign] = $item;
        return empty($sign) ? "  $key: $val" : "$sign $key: $val";
    }, $merged);
}

function genDiff($firstFilePath, $secondFilePath)
{
    $json1 = json_decode(file_get_contents($firstFilePath), true);
    $json2 = json_decode(file_get_contents($secondFilePath), true);
    print_r("{\n");
    array_map(fn($item) => print_r("$item\n"), combine($json1, $json2));
    print_r("}\n");
}
