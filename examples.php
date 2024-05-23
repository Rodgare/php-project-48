<?php



$array1 = [
    'a' => [
        'b' => 2,
        'c' => 3,
        'r' => [
            'tt' => 'deep'
        ]
    ],
    'd' => 4
];

$array2 = [
    'a' => [
        'b' => 2,
        'c' => 3,
        'r' => [
            'tt' => 'deepeer'
        ]
    ],
    'd' => 4
];
function arrayDiffAssocRecursive($array1, $array2, $sign = '')
{
    $difference = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key] = [$value, $sign];
            } else {
                $new_diff = arrayDiffAssocRecursive($value, $array2[$key], $sign);
                if (!empty($new_diff)) {
                    $difference[$key] = $new_diff;
                }
            }
        } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = [$value, $sign];
        }
    }

    return $difference;
}

print_r(arrayDiffAssocRecursive($array1, $array2, "-"));