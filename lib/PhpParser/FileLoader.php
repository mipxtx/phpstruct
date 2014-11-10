<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpParser;

class FileLoader
{

    /**
     * @var File
     */
    private $fileProcessor;

    public function __construct($filename) {

        if ($filename[0] == "~") {
            $filename = $_ENV["HOME"] . ltrim($filename, "~");
        }

        $iterator = new TokenIterator(token_get_all(file_get_contents($filename)));
        $this->fileProcessor = new File($filename, $iterator);
    }

    public function process() {
        return $this->fileProcessor->process();
    }

    public function enableDebug() {
        $this->fileProcessor->enableDebug();
    }
} 