<?php

$json1 = json_decode(file_get_contents('files/file1.json'), true);
$json2 = json_decode(file_get_contents('files/file2.json'), true);

print_r($json1);