<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

function getFixtureFullPath($fixtureName)
{
    $parts = [__DIR__, 'fixtures', $fixtureName];
    return realpath(implode('/', $parts));
}

class Test extends TestCase
{
    public function testGenDiff(): void
    {
        $file1 = getFixtureFullPath('file1.json');
        $file2 = getFixtureFullPath('file2.json');
        $actual = genDiff($file1, $file2);
        $expected = file_get_contents(getFixtureFullPath('testResult'));

        $this->assertEquals($expected, $actual);
        $this->assertEquals('', genDiff('', ''));
        $this->assertEquals('Wrong path', genDiff('wrongURL', 'badUrl'));
    }
}