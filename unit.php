<?php

/**
 * @author: mix
 * @date: 27.10.14
 */
class Unit extends PHPUnit_Framework_TestCase
{
    public function providerTest() {
        $out = [];

        $files = scandir(__DIR__ . "/test/source/");
        array_shift($files);
        array_shift($files);
        foreach ($files as $file) {

            $out[] = [$file];
        }

        return $out;
    }

    /**
     * @param $filename
     * @dataProvider providerTest
     */
    public function testTest($filename) {
        $f = new \PhpParser\FileLoader(__DIR__ . "/test/source/{$filename}");
        $expected = include __DIR__ . "/test/result/{$filename}";
        $actual = $f->getTree()->getCode()->first();
        $this->assertEquals($expected, $actual);
    }
}