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
        $fileJson1 = getFixtureFullPath('file1.json');
        $fileJson2 = getFixtureFullPath('file2.json');
        $fileYaml1 = getFixtureFullPath('file1.yaml');
        $fileYaml2 = getFixtureFullPath('file2.yaml');
        $actualJson = genDiff($fileJson1, $fileJson2);
        $actualYaml = genDiff($fileYaml1, $fileYaml2);
        $expected = file_get_contents(getFixtureFullPath('testResult'));

        $this->assertEquals($expected, $actualJson);
        $this->assertEquals($expected, $actualYaml);
        $this->assertEquals('', genDiff('', ''));
        $this->assertEquals('Wrong path', genDiff('wrongURL', 'badUrl'));
    }
}