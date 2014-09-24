<?php
namespace PhpStruct\Parser;

use PhpStruct\Struct\ClassField;
use PhpStruct\Struct\AbstractDef;
use PhpStruct\Struct\ClassDef;
use PhpStruct\Struct\InterfaceDef;
use PhpStruct\Struct\TraitDef;
use PhpStruct\Expression\Expresion;
use PhpStruct\Struct\Method;
use PhpStruct\Struct\ProcArgument;
use PhpStruct\Struct\Procedure;

/**
 * @author: mix
 * @date: 20.09.14
 */
class File
{

    private $path;

    private $file;

    /**
     * @var TokenIterator
     */
    private $iterator;

    /**
     * @var AbstractDef
     */
    private $currentDef;

    private $currentCode;

    private $debug = false;

    public function __construct($filename) {

        if ($filename[0] = "~") {
            $filename = $_ENV["HOME"] . ltrim($filename, "~");
        }
        $this->path = $filename;

        $this->file = new \PhpStruct\Struct\File($this->path);
    }

    public function getTree() {
        $iterator = $this->iterator = new TokenIterator(token_get_all(file_get_contents($this->path)));

        $open = $iterator->current();

        if (!$open->isTypeOf(T_OPEN_TAG)) {
            throw new FailException("start tag must be a first token, {$open} given in {$this->path}");
        }

        while (!$iterator->end()) {

            $token = $iterator->next();

            $this->currentDef = null;
            switch ($token->getType()) {
                case T_ABSTRACT :
                case T_FINAL :
                    if ($token->next()->isTypeOf(T_CLASS)) {
                        $this->currentDef = new ClassDef();
                        $iterator->next();
                    }
                    break;
                case T_CLASS :
                    $this->currentDef = new ClassDef();
                    break;
                case T_TRAIT :
                    $this->currentDef = new TraitDef();
                    break;
                case T_INTERFACE :

                    $this->currentDef = new InterfaceDef();
                    break;
            }
            if ($this->currentDef) {
                $this->file->addCass($this->currentDef);
                $this->processClassToken($token);
            } else {
                $this->processCodeToken($token);
            }
        }

        return $this->file;
    }

    public function processCodeToken(Token $token) {

        $iterator = $this->iterator;
        switch ($token->getType()) {
            case T_NAMESPACE;
                $value = $iterator->next();
                $iterator->next(); // ;
                $this->file->setNameSpace($value);
                break;
            //case T_USE :

            default:
                //echo $token->getType() . "\n";
                //echo $token->getValue() . "\n";
        }
    }

    public function processClassToken() {

        $token = $this->current();

        if ($token->isTypeOf(T_CLASS)) {
            /** @var ClassDef $current */
            $current = $this->currentDef;
            if ($token->prev()->isTypeOf(T_ABSTRACT)) {
                $current->setAbstract();
            }
            if ($token->prev()->isTypeOf(T_FINAL)) {
                $current->setFinal();
            }
        }

        $token = $this->next();
        $this->currentDef->setName($token->getValue());

        $token = $this->next();

        if ($token->isTypeOf(T_EXTENDS)) {
            do {
                $this->next();
                $this->currentDef->addExtends($this->current()->getValue());
                $this->next();
            } while (
                $token->getValue() !== "{"
                && $token->isTypeOf(T_IMPLEMENTS)
            );
        }

        if ($token->isTypeOf(T_IMPLEMENTS)) {
            do {
                $this->next();
                $this->currentDef->addExtends($this->current()->getValue());
                $this->next();
            } while (
                $token->getValue() !== "{"
            );
        }

        $this->next();

        while ($this->current()->getValue() !== "}") {

            $token = $this->current();
            $next = $token->next();
            $next2 = $token->next(2);

            if (false) {
                $this->enableDebug();
            }

            if (
                $token->isTypeOf(T_FINAL)
                || $token->isTypeOf(T_ABSTRACT)
                || $token->isTypeOf(T_FUNCTION)

                || $next->isTypeOf(T_FINAL)
                || $next->isTypeOf(T_ABSTRACT)
                || $next->isTypeOf(T_FUNCTION)

                || $next2->isTypeOf(T_FUNCTION)  // public static function
            ) {
                $this->log("\nprocessing method");
                $method = $this->processMethod();
                $this->currentDef->addMethod($method);
            } else {
                $this->log("\nprocessing field");
                $field = $this->processField();
                //echo "field " . $field->getName() . "\n";
                $this->currentDef->addField($field);
            }
        }
    }

    public function processField() {
        $token = $this->current();
        $static = false;

        if ($token->isTypeOf(T_STATIC)) {
            $static = true;
            $token = $this->next();
        }

        switch ($token->getType()) {
            case T_PROTECTED:
                $visible = "protected";
                break;
            case T_PRIVATE:
                $visible = "private";
                break;
            case T_VAR:
            case T_PUBLIC:
                $visible = "public";
                break;
            default:
                throw new FailException("Unknown token, $token while parsing {$this->path}");
        }

        $token = $this->next();

        if ($token->isTypeOf(T_STATIC)) {
            $static = true;
            $token = $this->next();
        }

        $name = $token->getValue();

        $field = new ClassField($visible, $name);
        if ($static) {
            $field->setStatic();
        }

        $token = $this->next();

        if ($token->getValue() != ";") {
            $this->next();
            $field->setDefault($this->processExpression());

            $token = $this->current();
            if ($token->getValue() == ";") {
                $this->next();
            }
            //echo "after expr $token / " . $token->next() . "\n";
            //$this->next();
        }

        return $field;
    }

    public function processMethod() {

        $token = $this->current();
        $static = false;
        $abstract = false;
        $final = false;
        $access = 'public';
        $name = "";

        do {

            switch ($token->getType()) {
                case T_STATIC :
                    $static = true;
                    break;
                case T_ABSTRACT :
                    $abstract = true;
                    break;
                case T_FINAL :
                    $final = true;
                    break;
                case T_PROTECTED :
                    $access = "protected";
                    break;
                case T_PRIVATE :
                    $access = 'private';
                    break;
                case T_PUBLIC :
                    break;
                default:
                    $name = $token->getValue();
            }

            $token = $this->next();
        } while ($token->getValue() !== "(");

        $method = new Method($name);
        $method->setAccess($access);
        if ($static) {
            $method->setStatic();
        }

        if ($abstract) {
            $method->setAbstract();
        }

        if ($final) {
            $method->setFinal();
        }

        $this->processFunction($method);

        return $method;
    }

    public function processExpression() {

        $level = 0;

        $this->log("   expr start");
        do {
            $token = $this->current();
            $next = $token->next();
            if ($token->getValue() == "(" || $token->getValue() == "[") {
                $level++;
            }

            if ($token->getValue() == ")" || $token->getValue() == "]") {
                $level--;
            }
            $this->log("   level $level");
            //echo "level $level: $token\n";

            $done =
                ($level <= 0)
                && (
                    $next->getValue() == ";"
                    || $next->getValue() == ","
                    || $next->getValue() == ")"
                );

            $this->next();
        } while (!$done);

        return new Expresion();
    }

    public function current() {
        return $this->iterator->current();
    }

    public function next() {
        return $this->iterator->next();
    }

    public function processFunction(Procedure $proc) {

        $this->next();

        while ($this->current()->getValue() != ")") {

            $this->log("\n start param");
            $type = null;
            if ($this->current()->getType() != T_VARIABLE) {
                $type = $this->current()->getValue();
                $this->next();
            }

            $name = $this->current()->getValue();
            $param = new ProcArgument($name, $type);
            $proc->addArg($param);
            $this->next();
            $this->log("  before def");

            if ($this->current()->getValue() == ",") {
                $this->next();
                $this->log("  after plain");
            } elseif ($this->current()->getValue() == "=") {
                $this->next();
                $param->setDefault($this->processExpression());

                if ($this->current()->getValue() == ",") {
                    $this->next();
                }

                $this->log("  after default");
            } elseif ($this->current()->getValue() == ")") {
                $this->log("  after end");
            } else {
                throw new FailException("func params parsing fail in {$this->path}");
            }
        };

        $this->next();
        $level = 0;
        $cnt = 0;

        do {
            $cnt++;

            if ($this->current()->getValue() == "{") {
                $level++;
            } elseif ($this->current()->getValue() == "}") {
                $level--;
            }
            $this->next();
        } while ($level > 0);
    }

    public function log($message, $token = null) {
        if (!$this->debug) {
            return;
        }
        if (!$token) {
            $token = $this->current();
        }

        $next = $token->next();
        $next2 = $token->next(2);

        echo "$message: $token | $next | $next2\n";
    }

    public function enableDebug() {
        $this->debug = true;
    }

    public function disableDebug() {
        $this->debug = false;
    }
}
