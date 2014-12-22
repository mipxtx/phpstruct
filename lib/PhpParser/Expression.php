<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpParser;

use PhpStruct\Expression\CaseDef;
use PhpStruct\Expression\CatchDef;
use PhpStruct\Expression\DeclareDef;
use PhpStruct\Expression\DefaultCase;
use PhpStruct\Expression\ConditionLoop;
use PhpStruct\Expression\Dereference;
use PhpStruct\Expression\SwitchExpr;
use PhpStruct\Expression\TryDef;
use PhpStruct\Expression\UseBlock;
use PhpStruct\Expression\UseMapping;
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
use PhpStruct\Expression\HasParamsInterface;
use PhpStruct\Expression\IfExpr;
use PhpStruct\Expression\IfSmall;
use PhpStruct\Expression\MultiOperand;
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
use PhpStruct\Struct\NamespaceDef;
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
        ["$"],
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
        ["exit", "die", "echo", "print", "return", "namespace", "throw", "as", "global"],
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

    public function checkFunction(Base $name) {
        if ($this->current()->getValue() == "(") {
            $out = new FunctionCall($name);
            $this->parseParams($out);

            return $out;
        }

        return $name;
    }

    public function parseString() {

        $this->log("process string");
        $value = "";

        do {
            $value .= $this->current()->getValue();
            $this->logNext("ns");
        } while ($this->current()->getType() == T_STRING || $this->current()->getType() == T_NS_SEPARATOR);

        $out = new DefineUsage($value);

        return $this->checkFunction($out);
    }

    public function parseVar() {
        $out = new Variable(ltrim($this->current()->getValue(), '$'));
        $this->logNext("var out");

        return $this->checkFunction($out);
    }

    public function parseStringTemplates($stopOnType) {
        $this->logNext("start quoted");

        $out = new QuotedString();
        while ($this->current()->getType() !== $stopOnType) {
            if (
                $this->current()->getType() !== T_ENCAPSED_AND_WHITESPACE
                && $this->current()->getType() !== T_CURLY_OPEN
            ) {
                $expr = $this->processToken();
                if ($this->current()->equal("->")) {
                    $this->logNext("quoted ->");
                    $expr = new Binary("->", $expr);
                    $op2 = new DefineUsage($this->current()->getValue());
                    $expr->setOperand($op2);
                    $this->logNext("->quoted");
                } elseif ($this->current()->equal("[")) {
                    $this->logNext("quoted[");
                    $op2 = new DefineUsage($this->current()->getValue());
                    $expr = new ArrayAccess($expr, $op2);
                    $this->logNext("2]quoted", 2);
                }

                $out->addElement($expr);
            } elseif ($this->current()->getType() === T_CURLY_OPEN) {
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

        return $out;
    }

    /**
     * @throws FailException
     * @return \PhpStruct\Base
     */
    public function processToken() {
        $token = $this->current();
        $this->log("token");

        $out = null;

        switch ($token->getType()) {
            case T_DIR:
            case T_FILE:
            case T_LINE:
            case T_STRING:
            case T_ISSET:
            case T_EMPTY:
            case T_UNSET:
            case T_METHOD_C:
            case T_CLASS_C:
            case T_FUNC_C:
            case T_LIST:
            case T_CLASS:
            case T_NS_SEPARATOR :
            case T_STATIC :
            case T_DECLARE :
            case T_STRING_VARNAME:
            case T_EVAL:
                $out = $this->parseString();
                break;
            case T_DNUMBER:
            case T_LNUMBER :
            case T_ENCAPSED_AND_WHITESPACE:
            case T_CONSTANT_ENCAPSED_STRING :
            case T_NUM_STRING:
                $out = new ScalarConst($token->getValue());
                $out->setType($token->getType());
                $this->logNext("scalar");
                break;
                break;
            case T_VARIABLE :
                $out = $this->parseVar();
                break;
            case T_NEW:
                $this->logNext("new");
                $out = $this->parseString();
                if (!$out instanceof FunctionCall) {
                    $out = new FunctionCall($out);
                }
                $out->setObjectCreate();
                break;
            case T_ARRAY :
                $out = new ArrayDef();
                $this->logNext("array");
                $this->parseParams($out);
                break;
            case Token::T_DOUBLE_QUOTE :
                $out = $this->parseStringTemplates(Token::T_DOUBLE_QUOTE);
                break;
            case T_START_HEREDOC:
                $out = $this->parseStringTemplates(T_END_HEREDOC);
                break;
            case Token::T_BACKTICK :
                $out = $this->parseStringTemplates(Token::T_BACKTICK);
                $out->setExecute();
                break;
            case T_DOLLAR_OPEN_CURLY_BRACES :
                $this->logNext("deref start");
                $out = new Dereference($this->processExpression());
                $this->logNext("deref end");
                break;
            case T_BREAK:
            case T_CONTINUE:
                $out = new CycleBreak($token->getValue());
                $this->logNext("break out");
                break;
            case T_FUNCTION:
                $out = $this->processFunction();
                break;
            case Token::T_DOLLAR :
                // ${
                $this->logNext("2\${", 2);
                $body = $this->processExpression();
                $out = new Dereference($body);
                $this->logNext('${ out');
                $this->log("ddd");
                break;
            default:
                throw new FailException("unknown token " . $this->getLogInfo());
        }

        return $out;
    }

    /**
     * @param HasParamsInterface $object
     * @return HasParamsInterface
     */
    public function parseParams(HasParamsInterface $object) {
        $this->logNext("start args");

        while (!$this->current()->equal(["]", ")"])) {
            $expr = $this->processExpression([Token::T_COMMA]);
            $object->addParam($expr);
            if (!$this->current()->equal(["]", ")"])) {
                $this->logNext("param");
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
            $this->logNext("brscope single");
        }
        $this->level--;

        return $scope;
    }

    public function processStop() {
        return $this->current()->getValue() == "}"
        || $this->current()->getType() == T_CASE
        || $this->current()->getType() == T_DEFAULT;
    }

    public function processIf() {
        $this->logNext("2if", 2);
        $ifCond = $this->processExpression();
        $this->logNext("if expr");
        $body = $this->processBracesScope();
        $if = new IfSmall($ifCond, $body);
        $expr = new IfExpr($if);
        while (
            $this->current()->isTypeOf(T_ELSEIF)
            || ($this->current()->isTypeOf(T_ELSE) && $this->current()->next()->isTypeOf(T_IF))
        ) {
            if ($this->current()->isTypeOf(T_ELSEIF)) {
                $this->logNext("2elseif", 2);
            } else {
                $this->logNext("3elseif", 3);
            }
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

    /**
     * @param Base $value
     * @throws FailException
     * @return ClassField
     */
    public function extractField(Base $value) {
        if ($value instanceof Binary) {
            $var = $value->getFirstOperand();
            $default = $value->getOperand();
        } else {
            $var = $value;
            $default = null;
        }

        if ($var instanceof HasNameInterface) {
            $field = new ClassField($var);
            $field->copyModifiers($value);
            $field->setDefault($default);

            return $field;
        }
        print_r($value);
        throw new FailException("unknown field class " . get_class($value));
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

        foreach ($ifCond->getScope() as $line) {
            if ($line instanceof Procedure) {
                $expr->addMethod($line);
            } elseif ($line instanceof UseBlock) {
                $expr->addUse($line);
            } elseif ($line instanceof HasNameInterface) {
                $field = $this->extractField($line);
                $expr->addField($field);
            } elseif ($line instanceof Binary) {
                $current = $line;
                $modifiers = $line->getModifiers();
                while ($current instanceof Binary && $current->getOperator() == ",") {
                    $field = $this->extractField($current->getOperand());
                    $field->setModifiers($modifiers);
                    $expr->addField($field);
                    $current = $current->getFirstOperand();
                }
                $field = $this->extractField($current);
                $field->setModifiers($modifiers);
                $expr->addField($field);
            } else {
                print_r($line);
                throw new FailException("unknown field class " . get_class($line));
            }
        }

        return $expr;
    }

    public function getProcParam() {
        $type = null;

        if ($this->current()->getType() != T_VARIABLE && $this->current()->getValue() != "&") {
            /** @var DefineUsage $typeVar */
            $typeVar = $this->parseString();
            $type = $typeVar->getName();
        }
        $isLink = false;
        if ($this->current()->getValue() == "&") {
            $isLink = true;
            $this->logNext("link param");
        }

        $name = ltrim($this->current()->getValue(), "$");
        $param = new ProcArgument($name, $type);

        if ($isLink) {
            $param->setLink();
        }

        $this->logNext("func arg value");

        if ($this->current()->getValue() == ",") {
            $this->logNext("func arg plain");
        } elseif ($this->current()->getValue() == "=") {

            $this->logNext("func arg def");

            $param->setDefault($this->processExpression([Token::T_COMMA]));

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
        $expr = new Procedure();
        $this->logNext("func");
        if ($this->current()->getValue() == "&") {
            $this->logNext("func  &");
            $expr->setLinkResult();
        }
        if ($this->current()->getValue() != "(") {
            $expr->setName($this->current()->getValue());
            $this->logNext("func name");
        }
        $this->logNext("func args");
        while ($this->current()->getValue() != ")") {
            $expr->addProcArg($this->getProcParam());
        };
        $this->logNext("func args end");
        if ($this->current()->getType() == T_USE) {
            $this->logNext("func use");
            $this->parseParams($expr);
        }
        //
        if ($this->current()->equal("{")) {
            $ifCond = $this->processBracesScope();
            $expr->setBody($ifCond);
        }

        return $expr;
    }

    public function processUse() {
        $this->logNext("use");
        $useBlock = new UseBlock();
        do {
            $val = $this->parseString();
            $expr = new UseLine($val);
            if ($this->current()->isTypeOf(T_AS)) {
                $this->logNext("use as");
                $expr->setAs($this->parseString());
            }
            $useBlock->addUse($expr);
            $stop = true;
            if ($this->current()->getValue() == ",") {
                $stop = false;
                $this->logNext("use ,");
            }
        } while (!$stop);

        if ($this->current()->equal("{")) {
            $this->logNext("use {");
            while (!$this->current()->equal("}")) {
                $map = new UseMapping($this->processExpression());
                if ($this->current()->isTypeOf(T_AS)) {
                    $this->logNext("use map as");
                    while (!$this->current()->equal(";")) {
                        if ($this->current()->isModifier()) {
                            $this->logNext("use modifier");
                            continue;
                        }
                        $cc = $this->current();
                        $name = $this->parseString();
                        $this->processModifiers($name, $cc);
                        $map->setAlias($name);
                    }
                }
                $this->logNext("use mapp ;");
                $useBlock->addMapping($map);
            }
            $this->logNext("use }");
        } else {
            $this->logNext("use end");
        }

        return $useBlock;
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
                    $cond = $this->processExpression([Token::T_COLON]);
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
        if ($this->current()->getType() == T_FINALLY) {
            $this->logNext("finally");
            $expr->setFinally($this->processBracesScope());
        }

        return $expr;
    }

    public function parseLoopCondition() {
        //$this->logNext("loop cond");
        $this->logNext("loop (");
        $cond = $this->processExpression();
        $this->logNext("loop )");

        return $cond;
    }

    public function processDo() {
        $this->logNext("do");
        $body = $this->processBracesScope();
        $type = strtolower($this->current()->getValue());
        $cond = $this->parseLoopCondition();

        return new ConditionLoop($body, $cond, $type);
    }

    public function processDeclare() {
        $this->logNext("declare");

        $cond = $this->parseLoopCondition();
        $ret = new DeclareDef($cond);
        $this->log("dec");

        if ($this->current()->equal("{")) {
            $body = $this->processBracesScope();
            $ret->setBody($body);
        }

        return $ret;
    }

    public function processWhile() {
        $type = strtolower($this->current()->getValue());
        $this->logNext("while");
        $cond = $this->parseLoopCondition();
        $body = $this->processBracesScope();
        $out = new ConditionLoop($body, $cond, $type);
        $out->setConditionFirst();

        return $out;
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

            if ($this->current()->isModifier()) {
                $this->logNext("modifier");
                continue;
            }

            if (
                $this->current()->getType() == T_STATIC
                && $this->current()->next()->getType() != T_DOUBLE_COLON /// not lsb
            ) {

                $this->logNext("modifier");
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
                    break;
                case T_WHILE :
                    $expr = $this->processWhile();
                    break;
                case Token::T_BRACE_OPEN :
                    $expr = $this->processBracesScope();
                    break;
                case T_DECLARE :
                    $expr = $this->processDeclare();
                    break;
                case T_NAMESPACE :
                    $this->logNext("namespace");
                    $name = $this->current()->equal("{") ? "" : $this->parseString()->getName();
                    $this->log("name space");
                    $expr = new NamespaceDef($name);
                    if ($this->current()->equal("{")) {
                        $body = $this->processBracesScope();
                        foreach ($body->getScope() as $line) {
                            $expr->addLine($line);
                        }
                    } else {
                        $this->logNext("ns;");
                    }
                    $this->log("end");
                    break;
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

    public function processBrackets() {
    }

    /**
     * @param array $stopOn
     * @throws FailException
     * @return \PhpStruct\Base
     */
    public function processExpression($stopOn = []) {
        $this->level++;

        $this->log("start expr");

        /** @var Operator $current */
        $current = null;
        $top = null;

        while (
            !$this->endExpression()
            && !in_array($this->current()->getType(), $stopOn)
        ) {
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
            } elseif (
                $token->isUnary()
                && !($current instanceof Binary && $current->getOperator() == "->")
                && !($token->equal('$') && $token->next()->equal("{"))
            ) {
                list($current, $top) = $this->processUnary($token, $top);
            } elseif ($token->isBinary()) {
                $isUnary = $token->canUnary()
                    && (
                        !$top
                        || !$current instanceof MultiOperand
                        || ($current instanceof MultiOperand && $current->getOperand() === null)
                    );

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

                if (!$top || ($current instanceof Operator && $current->getOperand() === null)) {
                    // array define
                    $array = new ArrayDef();
                    $this->parseParams($array);

                    if ($current) {
                        $current->setOperand($array);
                    } else {
                        $top = $array;
                    }
                } elseif (!($top instanceof Operator)) {
                    // array access
                    $this->logNext("[");
                    $top = $this->processArrayAccess($top);
                    $this->logNext("]");
                } else {
                    // array access
                    $this->logNext("[");
                    $current->setOperand($this->processArrayAccess($current->getOperand()));
                    $this->logNext("]");
                }
            } elseif ($token->equal("{")) {
                // array access
                $this->logNext("{");
                $top = $this->processArrayAccess($top);
                $this->logNext("}");
            } else {

                if ($top && !$top instanceof Operator) {
                    break;
                }

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
            $this->current()->isTypeOf(Token::T_SEMICOLON)
            || $this->current()->isTypeOf(Token::T_BRACE_CLOSE)
            || $this->current()->isTypeOf(Token::T_SQ_BRACKETS_CLOSE)
            || $this->current()->isTypeOf(Token::T_BRACKETS_CLOSE)
            || $this->current()->isTypeOf(T_AS)
            || $this->current()->isTypeOf(Token::T_COLON)
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