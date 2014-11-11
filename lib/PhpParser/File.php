<?php
namespace PhpParser;

use PhpStruct\Expression\Scope;

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

    public function process() {
        $file = new \PhpStruct\Struct\File($this->path);
        $open = $this->current();
        if (!$open->isTypeOf(T_OPEN_TAG)) {
            throw new FailException("start tag must be a first token, {$open} given in {$this->path}");
        }

        $this->next();

        $expressionProcessor = new Expression($this->getIterator());
        $definitionProcessor = new Definition($this->getIterator(), $expressionProcessor);

        if ($this->isEnabled()) {
            $expressionProcessor->enableDebug();
            $definitionProcessor->enableDebug();
        }

        while (!$this->end()) {
            $token = $this->current();
            if ($token->getType() == T_NAMESPACE) {
                $this->log("add NS");
                $value = $this->next();
                $this->next(); // ;
                $file->setNameSpace($value);
            } elseif ($token->isDefinition()) {
                $this->log("start def");
                $file->addCass($definitionProcessor->process());
                $this->log("after process class");
            } else {
                $this->log("start expr");
                $scope = new Scope();
                $expressionProcessor->process($scope);

                define("DEBUG",1);

                $file->mergeScope($scope);
            }
        }

        return $file;
    }
}


