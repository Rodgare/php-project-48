<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class Test extends TestCase
{
    public function testDiffer(): void
    {
        $file1 = "tests/fixtures/file1.json";
        $file2 = "tests/fixtures/file2.json";
        $actual = genDiff($file1, $file2);
        $expected = file_get_contents("tests/fixtures/testResult");
        $this->assertEquals($expected, $actual);

    }
}