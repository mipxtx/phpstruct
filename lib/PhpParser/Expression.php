<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpParser;

use PhpStruct\Code;
use PhpStruct\Expression\ArgsDefineInterface;
use PhpStruct\Expression\CaseDef;
use PhpStruct\Expression\CatchDef;
use PhpStruct\Expression\DefaultCase;
use PhpStruct\Expression\DoDef;
use PhpStruct\Expression\SwitchExpr;
use PhpStruct\Expression\TryDef;
use PhpStruct\FailException;
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
use PhpStruct\Expression\IfSmall;
use PhpStruct\Expression\MultiOperand;
use PhpStruct\Expression\ObjectCreate;
use PhpStruct\Expression\Operator;
use PhpStruct\Expression\QuotedString;
use PhpStruct\Expression\ScalarConst;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\Ternary;
use PhpStruct\Expression\Unary;
use PhpStruct\Expression\UnarySuffix;
use PhpStruct\Expression\UseLine;
use PhpStruct\Expression\Variable;
use PhpStruct\HasNameInterface;
use PhpStruct\Struct\AbstractDataType;
use PhpStruct\Struct\ClassField;
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
        ["&"],
        ["++", "--", "~", "(int)", "(integer)", "(float)", "(string)", "(array)", "(object)", "(bool)", "@"],
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
        ["as"],
    ];

    private $level = 0;

    public function getLogMsg($msg) {
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

        return $msg;
    }

    public function log($msg, $skipTokens = false) {
        $this->cLog($this->getLogMsg($msg), $skipTokens);
    }

    public function __construct(TokenIterator $iterator) {
        $this->setIterator($iterator);
    }

    public function processString($value) {
        if ($this->current()->getValue() == "(") {
            $out = new FunctionCall($value);
            $this->parseArgs($out);
        } else {
            $out = new DefineUsage($value);
        }

        return $out;
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
            case T_UNSET:
            case T_METHOD_C:
            case T_CLASS_C:
            case T_FUNC_C:
            case T_LIST:
                $string = $token->getValue();
                $this->logNext("string");
                $out = $this->processString($string);
                break;
            case T_NS_SEPARATOR :

                $string = "";
                $current = $this->current();
                do {
                    $string .= $current->getValue();
                    $current = $this->logNext("ns");
                } while ($current->getType() == T_STRING || $current->getType() == T_NS_SEPARATOR);
                $this->log("ns out");
                $out = $this->processString($string);
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
                if ($this->current()->getValue() == "(") {
                    $this->parseArgs($out);
                }
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
                throw new FailException("unknown token " . $this->getLogInfo());
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
                    $object->addArg($arg);
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

    public function processStop() {
        return $this->current()->getValue() == "}"
        || $this->current()->getType() == T_CASE
        || $this->current()->getType() == T_DEFAULT;
    }

    public function getFuncArgs() {
    }

    public function processIf() {
        $this->logNext("2if", 2);
        $ifCond = $this->processExpression();
        $this->logNext("if expr");
        $body = $this->processBracesScope();
        $if = new IfSmall($ifCond, $body);
        $expr = new IfExpr($if);
        while ($this->current()->getType() == T_ELSEIF) {
            $this->logNext("2if", 2);
            $elseIfCond = $this->processExpression();
            $this->logNext("elseif");
            $elseif = new IfSmall($elseIfCond, $this->processBracesScope());
            $expr->addElseIf($elseif);
        }

        if ($this->current()->getType() == T_ELSE) {
            $this->logNext("else");
            $expr->setElse($this->processBracesScope());
        }

        return $expr;
    }

    public function processForeach() {
        $this->logNext("2foreach", 2);
        $item = $this->processExpression();
        $this->logNext("foreach as");
        $iterator = $this->processExpression();
        $this->logNext("foreach body");
        $ifCond = $this->processBracesScope();

        return new ForEachDef($item, $iterator, $ifCond);
    }

    public function processFor() {
        $this->logNext("2for", 2);
        $def = $this->processExpression();
        $this->logNext("for cond");
        $cond = $this->processExpression();
        $this->logNext("for count");
        $count = $this->processExpression();
        $this->logNext("for out");
        $ifCond = $this->processBracesScope();

        return new ForDef($def, $cond, $count, $ifCond);
    }

    public function processClass() {
        $token = $this->current();
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
        $ifCond = $this->processBracesScope();

        //print_r($ifCond);

        foreach ($ifCond->getScope() as $line) {
            if ($line instanceof Procedure) {
                $expr->addMethod($line);
            } elseif ($line instanceof UseLine) {
                $expr->addUse($line);
            } else {
                if ($line instanceof Binary) {
                    $var = $line->getFirstOperand();
                    $default = $line->getOperand();
                } else {
                    $var = $line;
                    $default = null;
                }

                if ($var instanceof HasNameInterface) {
                    $field = new ClassField($var->getName());
                    $field->copyModifiers($line);
                    $field->setDefault($default);
                    $expr->addField($field);
                } else {
                    throw new FailException("unknown field class " . get_class($var));
                }
            }
        }

        return $expr;
    }

    public function getProcParam() {
        $type = null;
        if ($this->current()->getType() != T_VARIABLE) {
            $type = $this->current()->getValue();
            $this->logNext("func arg type");
        }

        $name = ltrim($this->current()->getValue(), "$");
        $param = new ProcArgument($name, $type);

        $this->logNext("func arg value");

        if ($this->current()->getValue() == ",") {
            $this->logNext("func arg plain");
        } elseif ($this->current()->getValue() == "=") {

            $this->logNext("func arg def");

            $param->setDefault($this->processExpression([","]));

            if ($this->current()->getValue() == ",") {
                $this->logNext("func arg end");
            }
        } elseif ($this->current()->getValue() == ")") {
        } else {
            throw new FailException("func params parsing " . $this->current());
        }

        return $param;
    }

    public function processFunction() {
        $this->logNext("func");
        $name = $this->current()->getValue();
        $expr = new Procedure($name);
        $this->logNext("func name");

        $this->logNext("func args");

        while ($this->current()->getValue() != ")") {
            $expr->addArg($this->getProcParam());
        };

        $this->logNext("func args end");
        //
        if ($this->current()->equal("{")) {
            $ifCond = $this->processBracesScope();
            $expr->setBody($ifCond);
        }

        return $expr;
    }

    public function processUse() {
        $this->logNext("use");
        $val = $this->processToken();
        $expr = new UseLine($val);

        if ($this->current()->isTypeOf(T_AS)) {
            $this->logNext("use as");
            $expr->setAs($this->processToken());
            $this->logNext("use as2");
        }
        if ($this->current()->equal("{")) {
            $ee = $this->processBracesScope();
            $expr->setMapping($ee);
        } else {
            $this->logNext("use out");
        }

        return $expr;
    }

    public function processSwitch() {
        $this->logNext("switch2", 2);
        $switchCond = $this->processExpression();
        $this->logNext("switch out");
        $expr = new SwitchExpr($switchCond);
        $this->logNext("switch body");

        while (!$this->current()->equal("}")) {
            switch ($this->current()->getType()) {
                case T_CASE :
                    $this->logNext("case");
                    $cond = $this->processExpression([":"]);
                    $this->logNext("case body");
                    $body = $this->process();
                    $case = new CaseDef($body, $cond);
                    $expr->addCase($case);
                    break;
                case T_DEFAULT :
                    $this->logNext("default2", 2);
                    $body = $this->process();
                    $def = new DefaultCase($body);
                    $expr->setDefault($def);
                    break;
                default:
                    throw new FailException("unknown token");
            }
        }
        $this->logNext("switch out");

        return $expr;
    }

    public function processTry() {
        $this->logNext("try");
        $body = $this->processBracesScope();
        $expr = new TryDef($body);
        while ($this->current()->getType() == T_CATCH) {
            $this->logNext("2catch", 2);
            $arg = $this->getProcParam();
            $this->logNext("catch param out");
            $body = $this->processBracesScope();
            $catch = new CatchDef($arg, $body);
            $expr->addCatch($catch);
        }

        return $expr;
    }

    public function processDo() {
        $this->logNext("do");
        $body = $this->processBracesScope();
        if ($this->current()->getType() == T_WHILE) {
            $type = "while";
        } else {
            $type = "until";
        }

        $this->logNext("do cond");
        $this->logNext("do (");
        $cond = $this->processExpression();
        $this->logNext("do )");

        return new DoDef($body, $cond, $type);
    }

    /**
     * @param bool $single
     * @throws FailException
     * @return Scope
     */
    public function process($single = false) {
        $scope = new Scope();
        while (!$this->end() && !$this->processStop()) {

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

            if (in_array(
                $this->current()->getType(),
                [T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL, T_ABSTRACT, T_CONST]
            )) {
                $this->next();
                continue;
            }

            $start = $token = $this->current();
            switch ($this->current()->getType()) {
                case T_IF :
                    $expr = $this->processIf();
                    break;
                case T_FOREACH :
                    $expr = $this->processForeach();
                    break;
                case T_FOR :
                    $expr = $this->processFor();
                    break;
                case T_CLASS :
                case T_INTERFACE:
                case T_TRAIT:
                    $expr = $this->processClass();
                    break;
                case T_FUNCTION:
                    $expr = $this->processFunction();
                    break;
                case T_USE:
                    $expr = $this->processUse();
                    break;
                case T_SWITCH :
                    $expr = $this->processSwitch();
                    break;
                case T_TRY :
                    $expr = $this->processTry();
                    break;
                case T_DO :
                    $expr = $this->processDo();
                default:
                    $expr = $this->processExpression();
            }

            $this->processModifiers($expr, $start);

            if ($start->hasBlankLine()) {
                $expr->setHeadBlankLine();
            }

            $comment = trim($start->getComment());
            if ($comment) {
                $expr->setComment($comment);
            }
            $scope->addExpression($expr);
            $this->log("process end");
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

                $isUnary = !$top || ($current instanceof MultiOperand && $current->getOperand() === null);
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
                    function ($operator, $operand) use ($then, $token) {
                        $res = new Ternary($operand, $then);
                        $res->setInitToken($token);

                        return $res;
                    },
                    $top
                );
            } elseif ($token->equal("(")) {
                $this->logNext("in (");
                $expression = $this->processExpression();
                $expression->setBrackets();
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

    /**
     * @param $msg
     * @param int $count
     * @return Token
     */
    public function logNext($msg, $count = 1) {
        $this->log("next $msg");

        return $this->next($count);
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

            if ($itOp && $this->getPriority($operator) <= $this->getPriority($itOp)) {
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
                case T_CONST :
                    $target->setConst();
                    break;
                default:
                    $exit = true;
            }
        } while (!$exit);
    }
}