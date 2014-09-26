<?php
namespace PhpStruct\Parser;

/**
 * @author: mix
 * @date: 20.09.14
 */
class File
{
    use SupportTrait;

    private $path;

    public function __construct($filename) {
        if ($filename[0] = "~") {
            $filename = $_ENV["HOME"] . ltrim($filename, "~");
        }
        $this->path = $filename;
        $iterator = new TokenIterator(token_get_all(file_get_contents($this->path)));
        $this->setIterator($iterator);
    }

    public function getTree() {
        $file = new \PhpStruct\Struct\File($this->path);
        $open = $this->current();
        if (!$open->isTypeOf(T_OPEN_TAG)) {
            throw new FailException("start tag must be a first token, {$open} given in {$this->path}");
        }
        $expressionProcessor = new Expression($this->getIterator());
        $this->next();
        $this->enableDebug();
        while (!$this->end()) {

            $token = $this->current();
            switch ($token->getType()) {
                case T_NAMESPACE;
                    $this->log("add NS");
                    $value = $this->next();
                    $this->next(); // ;
                    $file->setNameSpace($value);
                    break;
                case T_ABSTRACT :
                case T_FINAL :
                case T_CLASS :
                case T_TRAIT :
                case T_INTERFACE :
                    $this->log("start def");
                    $processor = new Definition($this->getIterator(), $expressionProcessor);
                    $file->addCass($processor->process());
                    $this->log("after process class");
                    break;
                default :
                    $this->log("start expr");
                    $file->addExp($expressionProcessor->processExpression());
            }
        }

        return $file;
    }
}
