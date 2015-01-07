<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpParser;

class FileLoader
{

    private $fileName = "";

    /**
     * @var File
     */
    private $fileProcessor;

    private $cacheDir = "";

    private $disableCache = false;



    public function __construct($filename, $cacheDir =  Helper::DEFAULT_CACHEDIR) {
        $this->fileName = Helper::getFulPath($filename);

        $this->cacheDir = Helper::getFulPath($cacheDir) . "/files/";
        Helper::mkdir($this->cacheDir);
    }

    public function getProcessor() {
        if (!$this->fileProcessor) {
            $iterator = new TokenIterator(token_get_all(file_get_contents($this->fileName)));
            $this->fileProcessor = new File($this->fileName, $iterator);
        }

        return $this->fileProcessor;
    }

    public function getCachePath() {
        return $this->cacheDir . str_replace(["\\", "/"], "_", $this->fileName);
    }

    /**
     * @return \PhpStruct\Struct\File
     * @throws FailException
     */
    public function getTree() {
        if (
            !$this->disableCache
            && file_exists($this->getCachePath())
            && filemtime($this->getCachePath()) > filemtime($this->fileName)
        ) {

            return require $this->getCachePath();
        }
        $out = $this->getProcessor()->process();
        file_put_contents($this->getCachePath(), "<?php\nreturn " . var_export($out, 1) . ";");

        return $out;
    }

    public function enableDebug() {
        $this->getProcessor()->enableDebug();
    }

    public function disableCache() {
        $this->disableCache = true;
    }
} 