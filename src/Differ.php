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

function genDiff(string $firstFilePath, string $secondFilePath): string
{
    $file1 = json_decode(file_get_contents($firstFilePath), true);
    $file2 = json_decode(file_get_contents($secondFilePath), true);
    $result = implode("\n" ,array_map(fn($item) => "  $item", combine($file1, $file2)));

    return "\n{\n" . $result . "\n}\n";
}
