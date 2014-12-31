<?php
namespace PhpParser;
use PhpStruct\Expression\InlineStr;
use PhpStruct\Expression\Shebang;
use PhpStruct\Struct\NamespaceDef;


/**
 * @author: mix
 * @date: 20.09.14
 */
class File
{
    use SupportTrait;

    private $path;

    public function __construct($filename, $iterator) {
        $this->path = $filename;
        $this->setIterator($iterator);
    }

    /**
     * @return \PhpStruct\Struct\File
     * @throws FailException
     */
    public function process() {
        $file = new \PhpStruct\Struct\File($this->path);
        $namespace = new NamespaceDef("");
        while(!$this->current()->isTypeOf(T_OPEN_TAG) && !$this->end()){
            if(strpos($this->current()->getValue(), "#!") === 0){
                $namespace->addLine(new Shebang());
            }else{
                $namespace->addLine(new InlineStr($this->current()->getValue()));
            }
            $this->logNext("start inline");
        }

        if($this->current()->isTypeOf(T_OPEN_TAG)) {
            $this->next();
        }

        $expressionProcessor = new Expression($this->getIterator());
        if ($this->isEnabled()) {
            $expressionProcessor->enableDebug();
        }

        while (!$this->end()) {
            $this->log("start expr");
            $scope = $expressionProcessor->process()->getScope();

            foreach ($scope as $line) {
                if($line instanceof NamespaceDef){
                    if(!$namespace->isEmpty()){
                        $file->addNamespace($namespace);
                    }
                    $namespace = $line;
                }else{
                    $namespace->addLine($line);
                }
            }
        }

        if(!$namespace->isEmpty()){
            $file->addNamespace($namespace);
        }


        return $file;
    }
}


