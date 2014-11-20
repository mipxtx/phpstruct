<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class Unary extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Unary $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $operand = $in->getOperand();

        if (!$operand) {
            return $in->getOperator();
        }

        $operator = $in->getOperator();

        $last = $operator[strlen($operator) - 1];

        $lastLetter = ($last > 'a' && $last < 'z') || ($last > 'A' && $last < 'Z');

        // space after letter in operator
        if (!$operand->hasBrackets() && $lastLetter) {
            $space = " ";
        } else {
            $space = "";
        }

        $out = $space . $this->processExpression($operand, $level);

        return $operator . $out;
    }
}