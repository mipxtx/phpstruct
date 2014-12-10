<?php
ini_set("display_errors", "On");
require __DIR__ . "/init-dev.php";

$path = "/Users/mix/wsp/mamba/.packages/AdminAnketa/Di.class.php";
//$path = __DIR__ . "/sample/mess.php";
//$path = __DIR__ . "/sample/class.php";
//$path  = __DIR__ . "/sample/micro.php";
//$path  = __DIR__ . "/sample/if.php";

$f = new \PhpParser\FileLoader($path);
$f->enableDebug();
$f->disableCache();
$code = $f->getTree();

echo get_class($code) . "\n";

//echo "\n" . print_r($code, 1);

echo "\n\n\ncode:\n" . (new \PhpDump\Processor())->process($code, -1);

echo "\ndone\n";


