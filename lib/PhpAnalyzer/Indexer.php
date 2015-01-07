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

    private $skip = [];

    private $cacheDir;

    private $debug = false;

    public function __construct($cache = Helper::DEFAULT_CACHEDIR) {
        $this->cacheDir = $cache;
    }

    public function addRoot($root) {
        $path = realpath(Helper::getFulPath($root));
        if (!file_exists($path)) {
            return;
        }
        $this->root[] = realpath(Helper::getFulPath($root)) . "/";
    }

    public function addSkipDir($skip) {
        $path = realpath(Helper::getFulPath($skip));
        if ($path) {
            $this->skip[] = $path . "/";
        }
    }

    public function create() {
        $files = [];
        $time0 = microtime(1);
        foreach ($this->root as $root) {
            $files = array_merge($files, $this->scanDir($root));
        }

        $count = count($files);
        echo "total files: " . $count . " found in " . round(microtime(1) - $time0, 2) . "s\n";

        $cc = 0;


        $time = microtime(1);
        foreach ($files as $file) {
            echo ".";
            $cc++;
            if ($cc % 100 == 0) {
                echo " " . round($cc / $count * 100) . "% ";
                echo round(memory_get_usage()/1024/1024,2) . "MB";
                echo "\n";
            }
            try {
                $f = new \PhpParser\FileLoader($file, $this->cacheDir);
                //$f->enableDebug();
                //echo $file . "\n";
                $f->getTree();
            } catch (\Exception $e) {
                echo "caught exception while parsing $file with message " . $e->getMessage() . " at " . $e->getFile()
                    . ":" . $e->getLine() . "\n" . Helper::buildTrace($e->getTrace());
                die();
            }
        }
        echo "\ndone in " . round(microtime(1) - $time,2) . "s\n";
    }

    public function scanDir($dir) {
        static $level = 0;
        static $summ = 0;

        if ($this->debug && $level == 1) {
            $time = microtime(1);
        }
        $files = scandir($dir);

        $out = [];

        if (in_array($dir, $this->skip)) {
            return $out;
        }

        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }

            if (is_link($dir . $file)) {
                continue;
            }

            if (is_dir($dir . $file)) {
                $level++;
                $out = array_merge($out, $this->scanDir($dir . $file . "/"));
                $level--;
            } else {
                $rr = explode(".", $file);
                $ext = array_pop($rr);
                if (in_array($ext, $this->files)) {
                    $out[] = $dir . $file;
                }
            }
        }

        if ($this->debug && $level == 1) {
            $fullTime = microtime(1) - $time;
            $summ += $fullTime;
            echo $dir . " " . round($fullTime, 4) . "s total: " . round($summ, 4) . "s\n";
        }

        return $out;
    }
}