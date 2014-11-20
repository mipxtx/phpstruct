<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class Binary extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Binary $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $first = $this->processExpression($in->getFirstOperand(), $level);
        $second = $this->processExpression($in->getOperand(), $level);

        $operator = $in->getOperator();
        $space = in_array($operator, ["->", "::"]) ? "" : " ";

        return $first . $space . $operator . $space . $second;
    }
}