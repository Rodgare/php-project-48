<?php

$arr = ['key' => 'val', 'key2' => 'val', 'key3' => 'val3'];

array_map(function($key, $val) {
    print_r("$key => $val");
}, array_keys($arr), $arr);

