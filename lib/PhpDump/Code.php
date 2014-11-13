<?php
/**
 * @author: mix
 * @date: 09.11.14
 */

namespace PhpDump;

use PhpParser\FailException;
use PhpStruct\Expression\ArrayAccess;
use PhpStruct\Expression\ArrayAppend;
use PhpStruct\Expression\ArrayDef;
use PhpStruct\Expression\Base;
use PhpStruct\Expression\Binary;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\ForEachDef;
use PhpStruct\Expression\FunctionCall;
use PhpStruct\Expression\HasScopes;
use PhpStruct\Expression\IfExpr;
use PhpStruct\Expression\ScalarConst;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\Unary;
use PhpStruct\Expression\Variable;

class Code
{

    private $tree;

    public function __construct(Base $tree) {
        $this->tree = $tree;
    }

    public function getCode() {
        return $this->process($this->tree, -1);
    }

    /**
     * @param $code
     * @param $level
     * @throws FailException
     * @return string
     */
    public function process(Base $code, $level) {

        $class = get_class($code);
        do {

            $name = $this->getName($class);
            if (method_exists($this, $this->getName($class))) {
                $out = call_user_func_array([$this, $name], [$code, $level]);
                if ($code->locked()) {
                    $out = "($out)";
                }

                return $out;
            }
            $class = get_parent_class($class);
        } while ($class);

        throw new FailException("processor for " . get_class($code) . " not found");
    }

    public function getName($className) {
        return "process" . str_replace(["PhpStruct", "\\"], "", $className);
    }

    public function getLevelShift($level) {
        $out = "";
        for ($i = 0; $i < $level; $i++) {
            $out .= "    ";
        }

        return $out;
    }

    public function processExpressionBinary(Binary $in, $level) {
        $first = $this->process($in->getFirstOperand(), $level);
        $second = $this->process($in->getOperand(), $level);

        $operator = $in->getOperator();
        $space = in_array($operator, ["->", "::"]) ? "" : " ";

        return
            ($in->locked() ? "(" : "")
            . $first . $space . $operator . $space . $second
            . ($in->locked() ? ")" : "");
    }

    public function processExpressionUnary(Unary $in, $level) {

        $operand = $in->getOperand();

        if (!$operand) {
            return $in->getOperator();
        }

        $operator = $in->getOperator();

        $last = $operator[strlen($operator) - 1];

        $lastLetter = ($last > 'a' && $last < 'z') || ($last > 'A' && $last < 'Z');

        // space after letter in operator
        if (!$operand->locked() && $lastLetter) {
            $space = " ";
        } else {
            $space = "";
        }

        $out = $space . $this->process($operand, $level);

        return $operator . $out;
    }

    public function processExpressionIfExpr(IfExpr $in, $level) {
        $out = "if (" . $this->process($in->getCondition(), $level) . ")";
        $out .= $this->processExpressionScope($in->getThen(), $level);

        $else = $in->getElse();
        if ($else) {
            $out .= " else" . $this->processExpressionScope($else, $level);
        }

        return $out;
    }

    /**
     * @param Scope $scope
     * @param $level
     * @return string
     * @throws FailException
     */
    public function processExpressionScope(Scope $scope, $level) {

        $out = "";
        if ($level >= 0) {
            $out = " {\n";
        }

        foreach ($scope->getScope() as $line) {
            $out .= $this->getLevelShift($level + 1) . $this->process($line, $level + 1);
            if (!($line instanceof HasScopes)) {
                $out .= ";";
            }
            $out .= "\n";
        }

        if ($level >= 0) {
            $out .= $this->getLevelShift($level) . "}";
        }

        return $out;
    }

    public function processExpressionFunctionCall(FunctionCall $in, $level) {
        $name = $in->getName();
        $args = [];
        foreach ($in->getArgs() as $arg) {
            $args[] = $this->process($arg, $level + 1);
        }

        return "{$name}(" . implode(",", $args) . ")";
    }

    public function processExpressionScalarConst(ScalarConst $in) {
        return $in->getName();
    }

    public function processExpressionArrayAccess(ArrayAccess $in, $level) {
        $var = $this->process($in->getVariable(), $level);
        $acc = $this->process($in->getAccess(), $level);

        return "{$var}[{$acc}]";
    }

    public function processExpressionForEachDef(ForEachDef $in, $level) {
        return
            "foreach(" . $this->process($in->getItem(), $level) . " as " . $this->process($in->getEach(), $level) . ")"
            .
            $this->processExpressionScope($in->getBody(), $level);
    }

    public function processExpressionArrayAppend(ArrayAppend $in, $level) {
        return $this->process($in->getVariable(), $level) . "[]";
    }

    public function processExpressionDefineUsage(DefineUsage $in) {
        return $in->getName();
    }

    public function processExpressionVariable(Variable $in) {

        return '$' . $in->getName();
    }
}