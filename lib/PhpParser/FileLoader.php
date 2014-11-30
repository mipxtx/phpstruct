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

    public function getFulPath($filename){
        if ($filename[0] == "~") {
            $filename = $_ENV["HOME"] . ltrim($filename, "~");
        }
        return $filename;
    }


    public function __construct($filename, $cacheDir = "~/.phpstruct/") {
        $this->fileName = $this->getFulPath($filename);
        $this->cacheDir = $this->getFulPath($cacheDir);


    }

    public function getProcessor(){
        if(!$this->fileProcessor){
            $iterator = new TokenIterator(token_get_all(file_get_contents($this->fileName)));
            $this->fileProcessor = new File($this->fileName, $iterator);
        }
        return $this->fileProcessor;
    }


    public function getCachePath(){
        return $this->cacheDir . str_replace(["\\", "/"], "_",  $this->fileName);
    }

    /**
     * @return \PhpStruct\Struct\File
     * @throws FailException
     */
    public function getTree() {
        if(file_exists($this->getCachePath())){
            return require $this->getCachePath();
        }
        $out = $this->getProcessor()->process();
        file_put_contents($this->getCachePath(), "<?php\nreturn " . var_export($out,1) . ";");
        return $out;
    }

    public function enableDebug() {
        $this->getProcessor()->enableDebug();
    }
} 