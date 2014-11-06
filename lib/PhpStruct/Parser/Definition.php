<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Parser;

use PhpStruct\Struct\ClassField;
use PhpStruct\Struct\AbstractDef;
use PhpStruct\Struct\ClassDef;
use PhpStruct\Struct\TraitDef;
use PhpStruct\Struct\InterfaceDef;
use PhpStruct\Struct\Method;
use PhpStruct\Struct\ProcArgument;
use PhpStruct\Struct\Procedure;

class Definition
{
    use SupportTrait;

    /**
     * @var AbstractDef
     */
    private $currentDef;

    private $expressionProcessor;

    public function __construct(TokenIterator $iterator, Expression $processor) {
        $this->expressionProcessor = $processor;
        $this->setIterator($iterator);

        $token = $this->current();

        switch ($token->getType()) {
            case T_ABSTRACT :
            case T_FINAL :
                if ($token->next()->isTypeOf(T_CLASS)) {
                    $this->currentDef = new ClassDef();
                    $this->next();
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
    }

    /**
     * @return AbstractDef
     */
    public function getCurrentDef() {
        return $this->currentDef;
    }

    /**
     * @return AbstractDef
     * @throws FailException
     */
    public function process() {

        $token = $this->current();

        if ($token->isTypeOf(T_CLASS)) {
            /** @var ClassDef $current */
            $current = $this->getCurrentDef();
            if ($token->prev()->isTypeOf(T_ABSTRACT)) {
                $current->setAbstract();
            }
            if ($token->prev()->isTypeOf(T_FINAL)) {
                $current->setFinal();
            }
        }

        $token = $this->next();
        $this->getCurrentDef()->setName($token->getValue());

        $token = $this->next();

        if ($token->isTypeOf(T_EXTENDS)) {
            do {
                $this->next();
                $this->getCurrentDef()->addExtends($this->current()->getValue());
                $this->next();
            } while (
                $token->getValue() !== "{"
                && $token->isTypeOf(T_IMPLEMENTS)
            );
        }

        if ($token->isTypeOf(T_IMPLEMENTS)) {
            do {
                $this->next();
                $this->getCurrentDef()->addExtends($this->current()->getValue());
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
                $this->getCurrentDef()->addMethod($method);
            } else {
                $this->log("\nprocessing field");
                $field = $this->processField();
                //echo "field " . $field->getName() . "\n";
                $this->getCurrentDef()->addField($field);
            }
        }

        return $this->getCurrentDef();
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
                throw new FailException("Unknown token, $token");
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
            $field->setDefault($this->expressionProcessor->process());

            $token = $this->current();
            if ($token->getValue() == ";") {
                $this->next();
            }
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
                $param->setDefault($this->expressionProcessor->process());

                if ($this->current()->getValue() == ",") {
                    $this->next();
                }

                $this->log("  after default");
            } elseif ($this->current()->getValue() == ")") {
                $this->log("  after end");
            } else {
                throw new FailException("func params parsing " . $this->current());
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
}