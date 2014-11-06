<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Parser;

use PhpStruct\Expression\ArrayDef;
use PhpStruct\Expression\Base;
use PhpStruct\Expression\Binary;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\FunctionCall;
use PhpStruct\Expression\HasArgsInterface;
use PhpStruct\Expression\IfExpr;
use PhpStruct\Expression\ObjectCreate;
use PhpStruct\Expression\Operator;
use PhpStruct\Expression\ScalarConst;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\Unary;
use PhpStruct\Expression\Variable;

class Expression
{
    use SupportTrait {
        SupportTrait::log as cLog;
    }

    /**
     * @see http://php.net/manual/ru/language.operators.precedence.php
     *
     * @var array
     */
    private $priority = [
        ["++", "--", "~", "(int)", "(float)", "(string)", "(array)", "(object)", "(bool)", "@"],
        ["instanceof"],
        ["!"],
        ["*", "/", "%"],
        ["+", "-", "."],
        ["<<", ">>"],
        ["<", "<=", ">", ">="],
        ["==", "!=", "===", "!==", "<>"],
        ["&"],
        ["^"],
        ["|"],
        ["&&"],
        ["||"],
        ["=", "+=", "-=", "*=", "/=", ".=", "%=", "&=", "|=", "^=", "<<=", ">>=", "=>"],
        ["and"],
        ["xor"],
        ["or"],
    ];

    private $level = 0;

    public function log($msg, $skipTokens = false) {

        $msg = str_replace("\t", "    ", $msg);

        $shift = "";
        for ($i = 0; $i < $this->level; $i++) {
            $shift .= " ";
        }

        //var_dump($msg);
        //var_dump(strpos($msg,"\n"));

        if (strpos($msg, "\n")) {

            $lines = explode("\n", $msg);
            $out = [];
            foreach ($lines as $i => $line) {
                $out[] = $shift . $line;
            }
            $msg = implode("\n", $out);
        } else {
            $msg = $shift . $msg;
        }

        $this->cLog($msg, $skipTokens);
    }

    public function __construct(TokenIterator $iterator) {
        $this->setIterator($iterator);
    }

    /**
     * @return Base
     */
    public function processToken() {
        $token = $this->current();
        $this->log("tocken");

        $out = null;

        switch ($token->getType()) {

            case T_DIR:
            case T_FILE:
            case T_STRING:
                if ($token->next()->getValue() == "(") {
                    $out = new FunctionCall($token->getValue());
                    $this->logNext("func call");
                    $this->parseArgs($out);
                } else {
                    $out = new DefineUsage($token->getValue());
                }
                break;
            case T_LNUMBER :
            case T_CONSTANT_ENCAPSED_STRING :
                $out = new ScalarConst($token->getValue());
                $out->setType($token->getType());

                break;
            case T_VARIABLE :
                $out = new Variable($token->getValue());
                $this->logNext("var out");
                break;
            case T_NEW:
                $out = new ObjectCreate($token->getValue());
                $this->logNext("var 2obj", 2);
                $this->parseArgs($out);
                break;
            case T_ARRAY :
                $out = new ArrayDef();
                $this->logNext("array");
                $this->parseArgs($out);
                break;
            case T_IF :
                $this->logNext("2if", 2);
                $body = $this->processExpression();
                $out = new IfExpr($body);
                $this->logNext("if expr");
                $out->addExpression($this->process());
                $this->logNext("if end");
                break;
            default:
                $this->log("unknown token");
                die();
        }

        return $out;
    }

    public function parseArgs(HasArgsInterface $object) {
        $this->log("start args");
        $this->logNext("start args");
        while ($this->current()->getValue() != ")") {
            $this->log("start arg");

            $arg = $this->processExpression();
            if ($this->current()->getValue() != ")") {
                $this->logNext("next arg");
            }
            $object->addArgument($arg);
        }

        $this->log("end args");
        $this->logNext("end args");

        return $object;
    }

    /**
     * @return Base
     */
    public function process() {
        $scope = new Scope();
        while (!$this->current()->isDefinition() && !$this->end()) {
            $this->log("process start");

            $scope->addExpression($this->processExpression());
            $this->log("process end");
        }

        return $scope->count() == 1 ? $scope->first() : $scope;
    }

    /**
     * @return Base
     */
    public function processExpression() {
        $this->level++;

        /** @var Operator $current */
        $current = null;
        $top = null;

        do {
            $token = $this->current();
            /*$this->log(
                "expr start ('" . $token->getValue() . "') dump: " .
                ($current instanceof Operator ? $current->dump(0) : 'NULL')
            //. "\n". var_export($current, 1) . "\n"
            );*/

            switch (strtolower($token->getValue())) {
                case  "." :
                case  "+" :
                case  "-" :
                case  "*" :
                case  "/" :
                case  "%" :
                case  "=" :
                case  "->" :
                case  "::" :
                case  "=>" :
                case  "&&" :
                case  "||" :
                    $this->logNext("binary");
                    list($current, $top) = $this->createOperator(
                        $token->getValue(),
                        function ($operator, $operand) {
                            return new Binary($operator, $operand);
                        },
                        $top
                    );
                    break;

                case "!" :
                case "require" :
                case "require_once" :
                case "include" :
                case "include_once" :
                case "return" :
                case "echo" :
                case "print" :
                    $this->logNext("unary");
                    list($current, $top) = $this->createOperator(
                        $token->getValue(),
                        function ($operator, $operand) {
                            return new Unary($operator, $operand);
                        },
                        $top
                    );
                    break;

                case "(" :
                    $this->logNext("in (");
                    $expression = $this->processExpression();
                    if ($expression instanceof Operator) {
                        $expression->lock();
                    }
                    $this->logNext("out (");

                    if ($current) {
                        $current->setOperand($expression);
                    } else {
                        $top = $expression;
                    }
                    break;

                default :
                    $expression = $this->processToken();
                    if ($current) {
                        $current->setOperand($expression);
                    } else {
                        $top = $expression;
                    }
                    break;
            }

            /*$this->log(
                "expr end ('"
                . $token->getValue() . "') dump: "
                . ($current ? $current->dump(0) : $top->dump(0))
            );*/
        } while (!$this->endExpression());

        $this->level--;

        return $top;
    }

    public function endExpression() {
        return
            $this->current()->getValue() == ";"
            || $this->current()->getValue() == ","
            || $this->current()->getValue() == "}"
            || $this->current()->getValue() == ")"
            || $this->end();
    }

    public function logNext($msg, $count = 1) {
        $this->log("next $msg");
        $this->next($count);
    }

    public function dump(Base $expr = null) {
        return ($expr instanceof Base ? $expr->dump(0) : 'NULL');
    }

    /**
     * @param string $token
     * @return int|string
     */
    public function getPriority($token) {
        $token = strtolower($token);
        foreach ($this->priority as $i => $items) {
            if (in_array($token, $items)) {
                return count($this->priority) - $i;
            }
        }
        return 9999;
    }

    /**
     * @param string $operator
     * @param callable $factory
     * @param Base $top
     * @return Operator[]
     */
    public function createOperator($operator, callable $factory, $top) {
        $i = 0;
        $iteration = $top;

        $prev = null;

        while ($iteration instanceof Operator) {

            if ($iteration->locked()) {
                break;
            }

            $itOp = $iteration->getOperator();

            if ($this->getPriority($operator) <= $this->getPriority($itOp)) {
                $this->log("iteration stop: ". $iteration->dump(0) .
                    " $operator(" . $this->getPriority($operator) . ") >= " . $itOp . "(" . $this->getPriority($itOp) . ")",
                    true
                );
                break;
            }

            $this->log("iteration next: ". $iteration->dump(0) .
                " $operator(" . $this->getPriority($operator) . ") < " . $itOp . "(" . $this->getPriority($itOp) . ")",
                true
            );

            $i++;
            $prev = $iteration;
            $iteration = $iteration->getOperand();
        }

        if ($prev == null) {
            /** @var Operator $current */
            $current = $factory($operator, $top);
            $top = $current;
        } else {
            $arg = $prev->getOperand();
            /** @var Operator $current */
            $current = $factory($operator, $arg);
            $prev->setOperand($current);
        }

        $this->log("top now: " . $top->dump(0),1);
        $this->log("current now: " . $current->dump(0),1);

        return [$current, $top];
    }
}