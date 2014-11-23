<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpParser;

use PhpDump\Code;
use PhpStruct\Expression\ArrayAccess;
use PhpStruct\Expression\ArrayAppend;
use PhpStruct\Expression\ArrayDef;
use PhpStruct\Base;
use PhpStruct\Expression\Binary;
use PhpStruct\Expression\CycleBreak;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\EmptyStatement;
use PhpStruct\Expression\ForDef;
use PhpStruct\Expression\ForEachDef;
use PhpStruct\Expression\FunctionCall;
use PhpStruct\Expression\HasArgsInterface;
use PhpStruct\Expression\IfExpr;
use PhpStruct\Expression\ObjectCreate;
use PhpStruct\Expression\Operator;
use PhpStruct\Expression\QuotedString;
use PhpStruct\Expression\ScalarConst;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\Ternary;
use PhpStruct\Expression\Unary;
use PhpStruct\Expression\UnarySuffix;
use PhpStruct\Expression\Variable;
use PhpStruct\Struct\AbstractDataType;
use PhpStruct\Struct\ClassField;
use PhpStruct\Struct\ModifiersInterface;
use PhpStruct\Struct\ProcArgument;
use PhpStruct\Struct\Procedure;

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
        ["->"],
        ["clone"],
        ["["],
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
        ["?"],
        ["include", "include_once", "require", "require_once"],
        ["=", "+=", "-=", "*=", "/=", ".=", "%=", "&=", "|=", "^=", "<<=", ">>=", "=>"],
        ["and"],
        ["xor"],
        ["or"],
        [","],
        ["exit", "die", "echo", "print", "return"],
        ["public", "protected", "private", "abstract", "final", "static"]
    ];

    private $level = 0;

    public function log($msg, $skipTokens = false) {

        foreach (["next" => "0;31", "start" => "0;32", "token" => "1;33", "arg " => "0;34"] as $key => $color) {
            $msg = str_replace($key, "\033[{$color}m{$key}\033[0m", $msg);
        }
        $msg = str_replace("\t", "    ", $msg);
        $shift = "";
        for ($i = 0; $i < $this->level; $i++) {
            $shift .= " ";
        }
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
     * @return \PhpStruct\Base
     */
    public function processToken() {
        $token = $this->current();
        $this->log("token");

        $out = null;

        switch ($token->getType()) {
            case T_DIR:
            case T_FILE:
            case T_STRING:
            case T_ISSET:
            case T_EMPTY:
                if ($token->next()->getValue() == "(") {
                    $out = new FunctionCall($token->getValue());
                    $this->logNext("func call");
                    $this->parseArgs($out);
                } else {
                    $out = new DefineUsage($token->getValue());
                    $this->logNext("define");
                }
                break;
            case T_LNUMBER :
            case T_ENCAPSED_AND_WHITESPACE:
            case T_CONSTANT_ENCAPSED_STRING :
                $out = new ScalarConst($token->getValue());
                $out->setType($token->getType());
                $this->logNext("scalar");
                break;
            case T_VARIABLE :
                $out = new Variable(ltrim($token->getValue(), '$'));
                $this->logNext("var out");
                break;
            case T_NEW:
                $out = new ObjectCreate($token->next()->getValue());
                $this->logNext("var 2obj", 2);
                $this->parseArgs($out);
                break;
            case T_ARRAY :
                $out = new ArrayDef();
                $this->logNext("array");
                $this->parseArgs($out);
                break;
            case Token::T_QUOTE :
                $this->logNext("start quoted");

                $out = new QuotedString();
                while (!$this->current()->equal('"')) {
                    if ($this->current()->equal("{")) {
                        $this->logNext("quoted expr");
                        $expr = $this->processExpression();
                        $out->addElement($expr);
                        $this->logNext("quoted expr out");
                    } else {
                        $expr = $this->processToken();
                        $out->addElement($expr);
                    }
                }
                $this->logNext("quoted out");
                break;
            case T_BREAK:
            case T_CONTINUE:
                $out = new CycleBreak($token->getValue());
                $this->logNext("break out");
                break;
            default:
                $this->log("unknown token");
                die();
        }

        return $out;
    }

    /**
     * @param HasArgsInterface $object
     * @return HasArgsInterface
     */
    public function parseArgs(HasArgsInterface $object) {
        $this->logNext("start args");

        if (!$this->current()->equal(["]", ")"])) {
            $expr = $this->processExpression();
            if ($expr instanceof Base) {
                $args = [];
                while ($expr instanceof Binary && $expr->getOperator() == ",") {
                    $operand = $expr->getOperand();
                    if ($operand instanceof Base) {
                        $args[] = $operand;
                    }
                    $expr = $expr->getFirstOperand();
                }
                if ($expr instanceof Base) {
                    $args[] = $expr;
                }

                foreach (array_reverse($args) as $arg) {
                    $object->addArgument($arg);
                }
            }
        }
        $this->logNext("end args");

        return $object;
    }

    /**
     * @return Scope
     */
    public function processBracesScope() {

        $this->level++;
        if ($this->current()->getValue() == "{") {

            $this->logNext("brscope start");
            $scope = $this->process(false);
            $this->logNext("brscope end");
        } else {
            $scope = $this->process(true);
        }
        $this->level--;

        return $scope;
    }

    /**
     * @param bool $single
     * @throws FailException
     * @return Scope
     */
    public function process($single = false) {
        $scope = new Scope();
        while (!$this->end() && $this->current()->getValue() != "}") {

            if ($this->current()->getValue() == ";") {
                if ($single) {
                    break;
                }
                $this->next();
                continue;
            }
            if ($this->current()->getValue() == "}") {
                break;
            }
            $this->log("process start");
            $expr = null;

            $start = $token = $this->current();
            switch ($this->current()->getType()) {
                case T_IF :
                    $this->logNext("2if", 2);
                    $body = $this->processExpression();
                    $this->logNext("if expr");
                    $then = $this->processBracesScope();
                    $expr = new IfExpr($body, $then);
                    if ($this->current()->getType() == T_ELSE) {
                        $this->logNext("else");
                        $expr->setElse($this->processBracesScope());
                    }
                    break;
                case T_FOREACH :
                    $this->logNext("2foreach", 2);
                    $item = $this->processExpression();
                    $this->logNext("foreach as");
                    $iterator = $this->processExpression();
                    $this->logNext("foreach body");
                    $body = $this->processBracesScope();
                    $expr = new ForEachDef($item, $iterator, $body);
                    break;
                case T_FOR :
                    $this->logNext("2for", 2);
                    $def = $this->processExpression();
                    $this->logNext("for cond");
                    $cond = $this->processExpression();
                    $this->logNext("for count");
                    $count = $this->processExpression();
                    $this->logNext("for out");
                    $body = $this->processBracesScope();
                    $expr = new ForDef($def, $cond, $count, $body);
                    break;
                case T_CLASS :
                case T_INTERFACE:
                case T_TRAIT:

                    $this->logNext("class start");
                    $name = $this->current()->getValue();
                    $this->log("name $name");
                    $expr = new AbstractDataType(strtolower($token->getValue()), $name);
                    $type = "";
                    do {
                        switch (strtolower($this->current()->getValue())) {
                            case "extends" :
                            case "implements":
                                $type = strtolower($this->current()->getType());
                                break;
                            case "," :
                                break;
                            default :
                                if ($type == "extends") {
                                    $expr->addExtends($this->current()->getValue());
                                } elseif ($type == "implements") {
                                    $expr->addImplements($this->current()->getValue());
                                }
                                break;
                        }
                        $this->logNext("extends");

                    } while (!$this->current()->equal("{"));

                    $body = $this->processBracesScope();

                    foreach ($body->getScope() as $line) {
                        if ($line instanceof Procedure) {
                            $expr->addMethod($line);
                        } else {



                            if ($line instanceof Binary) {
                                $var = $line->getFirstOperand();
                                $default = $line->getOperand();
                            } else {
                                $var = $line;
                                $default = null;
                            }
                            $field = new ClassField($var);
                            $field->copyModifiers($line);
                            $field->setDefault($default);

                            $expr->addField($field);

                        }
                    }

                    break;
                case T_FUNCTION:
                    $this->logNext("func");
                    $name = $this->current()->getValue();
                    $expr = new Procedure($name);
                    $this->logNext("func name");
                    $this->logNext("func args");

                    while ($this->current()->getValue() != ")") {

                        $type = null;
                        if ($this->current()->getType() != T_VARIABLE) {
                            $type = $this->current()->getValue();
                            $this->logNext("func arg type");
                        }

                        $name = ltrim($this->current()->getValue(), "$");
                        $param = new ProcArgument($name, $type);
                        $expr->addArg($param);
                        $this->logNext("func arg value");

                        if ($this->current()->getValue() == ",") {
                            $this->logNext("func arg plain");
                        } elseif ($this->current()->getValue() == "=") {

                            $this->logNext("func arg def");

                            $param->setDefault($this->processExpression([","]));

                            if ($this->current()->getValue() == ",") {
                                $this->logNext("func arg end");
                            }
                        }elseif($this->current()->getValue() == ")"){
                        } else {
                            throw new FailException("func params parsing " . $this->current());
                        }
                    };

                    $this->logNext("func args end");

                    //
                    if($this->current()->equal("{")) {
                        $body = $this->processBracesScope();
                        $expr->setBody($body);
                    }

                    break;

                case T_VAR:
                case T_PUBLIC:
                case T_PROTECTED:
                case T_PRIVATE:
                case T_STATIC:
                case T_FINAL:
                case T_ABSTRACT:
                    $this->next();
                    break;

                default:
                    $expr = $this->processExpression();
            }
            if ($expr) {
                $this->processModifiers($expr, $start);

                if ($start->hasBlankLine()) {
                    $expr->setHeadBlankLine();
                }

                $expr->setComment(trim($start->getComment()));
                $scope->addExpression($expr);
                $this->log("process end");
            }
        }

        return $scope;
    }

    public function processUnary(Token $token, $top) {
        $this->logNext("unary");
        list($current, $top) = $this->createOperator(
            $token->getValue(),
            function ($operator, $operand) {
                return new Unary($operator, $operand);
            },
            $top
        );

        return [$current, $top];
    }

    /**
     * @return \PhpStruct\Base
     */
    public function processExpression(array $stopOn = []) {
        $this->level++;

        if ($this->current()->equal("}")) {
            die();
        }

        $this->log("start expr");

        /** @var Operator $current */
        $current = null;
        $top = null;

        while (!$this->endExpression() && !in_array($this->current()->getValue(), $stopOn)) {
            $token = $this->current();
            if ($token->unarySuffix()) {
                if ($top && !$current) {
                    $old = $top;
                    $top = $current = new UnarySuffix($token->getValue());
                    $current->setOperand($old);
                    $this->logNext("usuffix");
                } elseif ($current && $current->getOperand() !== null) {
                    $old = $current->getOperand();
                    $new = new UnarySuffix($token->getValue());
                    $new->setOperand($old);
                    $current->setOperand($new);
                    $this->logNext("usuffix");
                } else {
                    list($current, $top) = $this->processUnary($token, $top);
                }
            } elseif ($token->isUnary()) {
                list($current, $top) = $this->processUnary($token, $top);
            } elseif ($token->isBinary()) {

                $isUnary = !$top || ($current instanceof Binary && $current->getOperand() === null);

                if ($isUnary) {
                    list($current, $top) = $this->processUnary($token, $top);
                } else {
                    $this->logNext("binary");
                    list($current, $top) = $this->createOperator(
                        $token->getValue(),
                        function ($operator, $operand) {
                            return new Binary($operator, $operand);
                        },
                        $top
                    );
                }
            } elseif ($token->equal("?")) {
                $this->logNext("ternary");
                $then = $this->processExpression();
                $this->logNext("ternary 2");
                list($current, $top) = $this->createOperator(
                    $token->getValue(),
                    function ($operator, $operand) use ($then) {
                        return new Ternary($operand, $then);
                    },
                    $top
                );
            } elseif ($token->equal("(")) {
                $this->logNext("in (");
                $expression = $this->processExpression();
                $expression->brackets();
                $this->logNext("out (");

                if ($current) {
                    $current->setOperand($expression);
                } else {
                    $top = $expression;
                }
            } elseif ($token->equal("[")) {

                $operand = null;

                if (!($top instanceof Operator)) {
                    // array access
                    $this->logNext("[");
                    $top = $this->processArrayAccess($top);
                    $this->logNext("]");
                } elseif (!$current || $current->getOperand() === null) {
                    // array define
                    $array = new ArrayDef();
                    $this->parseArgs($array);

                    if ($current) {
                        $current->setOperand($array);
                    } else {
                        $top = $array;
                    }
                } else {
                    // array access
                    $this->logNext("[");
                    $current->setOperand($this->processArrayAccess($current->getOperand()));
                    $this->logNext("]");
                }
            } else {
                $expression = $this->processToken();
                if ($current) {
                    $current->setOperand($expression);
                } else {
                    $top = $expression;
                }
            }
        }
        $this->log("end expr");
        $this->level--;

        return $top ?: new EmptyStatement();
    }

    public function processArrayAccess(Base $operand) {
        if ($this->current()->getValue() == "]") {
            $array = new ArrayAppend($operand);
        } else {
            $array = new ArrayAccess($operand, $this->processExpression());
        }

        return $array;
    }

    public function endExpression() {
        return
            $this->current()->getValue() == ";"
            || $this->current()->getValue() == "}"
            || $this->current()->getValue() == ")"
            || $this->current()->getValue() == "]"
            || $this->current()->getValue() == "as" /// foreach
            || $this->current()->getValue() == ":" /// ternary
            || $this->end();
    }

    public function logNext($msg, $count = 1) {
        $this->log("next $msg");
        $this->next($count);
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
     * @param \PhpStruct\Base $top
     * @return Operator[]
     */
    public function createOperator($operator, callable $factory, $top) {

        $prev = $this->getContainer($operator, $top);

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

        return [$current, $top];
    }

    public function getContainer($operator, $top) {
        $iteration = $top;

        $prev = null;
        while ($iteration instanceof Operator) {
            if ($iteration->hasBrackets()) {
                break;
            }
            $itOp = $iteration->getOperator();
            if ($this->getPriority($operator) <= $this->getPriority($itOp)) {
                break;
            }
            $prev = $iteration;
            $iteration = $iteration->getOperand();
        }

        return $prev;
    }

    public function processModifiers(Base $target, Token $current) {

        do {
            $current = $current->prev();
            $exit = false;
            switch ($current->getType()) {
                case T_VAR:
                case T_PUBLIC:
                case T_PROTECTED:
                case T_PRIVATE:
                    $target->setVisibility(strtolower($current->getValue()));
                    break;
                case T_ABSTRACT:
                    $target->setAbstract();
                    break;
                case T_FINAL:
                    $target->setFinal();
                    break;
                case T_STATIC:
                    $target->setStatic();
                    break;
                default:
                    $exit = true;
            }
        } while (!$exit);
    }
}