<?php
namespace PhpParser;
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

        $open = $this->current();
        if (!$open->isTypeOf(T_OPEN_TAG)) {
            throw new FailException("start tag must be a first token, {$open} given in {$this->path}");
        }
        $this->next();
        $expressionProcessor = new Expression($this->getIterator());
        if ($this->isEnabled()) {
            $expressionProcessor->enableDebug();
        }
        $namespace = new NamespaceDef("");
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


