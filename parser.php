<?php
ini_set("display_errors", "On");
require __DIR__ . "/init-dev.php";

//$path = __DIR__ . "/sample/mess.php";
//$path = __DIR__ . "/sample/class.php";
$path  = __DIR__ . "/sample/micro.php";
//$path  = __DIR__ . "/sample/if.php";
$path = "/Users/mix/wsp/mamba/.composer/vendor/zendframework/zend-validator/Zend/Validator/Hostname/Cn.php";
//$path = __DIR__ . "/sample/short.php";
$f = new \PhpParser\FileLoader($path);
$f->enableDebug();
$f->disableCache();
$code = $f->getTree();

//echo get_class($code) . "\n";

//echo "\n" . print_r($code, 1);

echo "\n\n\ncode:\n" . (new \PhpDump\Processor())->process($code, -1);

echo "\n". round(memory_get_usage(1)/1024/1024,2) . "MB";

echo "\ndone\n";


