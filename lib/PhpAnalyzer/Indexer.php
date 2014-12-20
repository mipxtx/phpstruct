<?php
/**
 * @author: mix
 * @date: 24.11.14
 */

namespace PhpAnalyzer;

use PhpParser\Helper;

class Indexer
{

    private $files = ["php", "phtml", "inc"];

    private $root = [];

    private $cacheDir;

    public function __construct($cache) {
        $this->cacheDir = realpath($cache);
        $this->mkdir($this->cacheDir);
    }

    public function addRoot($root) {
        if (!file_exists($root)) {
            return;
        }
        $this->root[] = realpath($root) . "/";
    }

    public function create() {
        $files = [];

        foreach ($this->root as $root) {
            $files = array_merge($files, $this->scanDir($root));
        }

        foreach ($files as $file) {
            try {
                $f = new \PhpParser\FileLoader($file, $this->cacheDir);
                //$f->enableDebug();
                echo $file . "\n";
                $f->getTree();
            } catch (\Exception $e) {
                echo "caught exception while parsing $file with message " . $e->getMessage() . " at " . $e->getFile()
                    . ":" . $e->getLine() . "\n" . Helper::buildTrace($e->getTrace());
                die();
            }
        }
    }

    public function scanDir($dir) {
        $files = scandir($dir);
        array_shift($files);
        array_shift($files);

        $out = [];

        foreach ($files as $file) {
            if (is_dir($dir . $file)) {
                $out = array_merge($out, $this->scanDir($dir . $file . "/"));
            } else {
                $rr = explode(".", $file);
                $ext = array_pop($rr);
                if (in_array($ext, $this->files)) {
                    $out[] = $dir . $file;
                }
            }
        }

        return $out;
    }

    public function mkdir($dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}