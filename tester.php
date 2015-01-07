<?php
/**
 * @author: mix
 * @date: 29.11.14
 */
ini_set("display_errors", "On");

require __DIR__ . "/init-dev.php";

$indexer = new \PhpAnalyzer\Indexer("~/wsp/phpstructcache");
$indexer->addRoot("~/wsp/mamba");
$indexer->addSkipDir("~/wsp/mamba/static");
$indexer->addSkipDir("~/wsp/mamba/clientside");
$indexer->addSkipDir("~/wsp/mamba/.git");

$indexer->addSkipDir("~/wsp/mamba/PreGenerated");
$indexer->addRoot("~/wsp/mamba/PreGenerated/lang_constants");
$indexer->create();

