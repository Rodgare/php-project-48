<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class Test extends TestCase
{
    public function testDifferPlainStyle(): void
    {
        print_r(genDiff("files/file1.json", "files/file1.json"));
    }
}