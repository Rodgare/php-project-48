<?php



$array1 = [
    'a' => [
        'r' => [
            'tt' => true,
            'zz' => false,
            'cc' => 'deepeer',
            'r' => 'deepeer',
            'aa' => 'deepeer',
            'ks' => 'deepeer'
        ],
        'b' => 2,
        'c' => 3
        
    ],
    'd' => 4,
    'yoo' => [
        'new' => 'cool'
    ]
];

$array2 = [
    'a' => [
        'r' => [
            'tt' => true,
            'zz' => false,
            'cc' => 'deepeer',
            'r' => 'deepeer',
            'aa' => 'deepeer',
            'ks' => 'deepeer'
        ],
        'b' => 2,
        'c' => 3
        
    ],
    'd' => 4,
    'yoo' => [
        'new' => 'huiul'
    ]
];

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

print_r(
    sorting($array1)
);