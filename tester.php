<?php
/**
 * @author: mix
 * @date: 29.11.14
 */
ini_set("display_errors", "On");
require __DIR__ . "/init-dev.php";

$path = "/Users/mix/wsp/mamba/.packages/Anketa/Anketa.inc";
//$path = __DIR__ . "/sample/if.php";
$f = new \PhpParser\FileLoader($path, __DIR__ ."/cache/");


$out = $f->getTree();

/*
$tester = new \PhpAnalyzer\Tester($f->getTree()->getClasses()[0]);
$tester->createTests();
*/