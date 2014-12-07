<?php
/**
 * @author: mix
 * @date: 29.11.14
 */
ini_set("display_errors", "On");
require __DIR__ . "/init-dev.php";

//$path = "/Users/mix/wsp/mamba/.packages/Anketa/Anketa.inc";
//$path = __DIR__ . "/sample/if.php";
//$path = __DIR__ . "/sample/method.php";
//$f = new \PhpParser\FileLoader($path, __DIR__ . "/cache/");
//$f->disableCache();
//$f->enableDebug();
//$out = $f->getTree()->getClasses();

//print_R($out);

//$tester = new \PhpAnalyzer\Tester($out[0]);
//$tester->createTests();

$indexer = new \PhpAnalyzer\Indexer(__DIR__ . "/cache/");
$indexer->addRoot("/Users/mix/wsp/mamba/.packages");
$indexer->create();
