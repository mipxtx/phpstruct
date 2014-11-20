<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class Ternary extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Ternary $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $this->processExpression($in->getIf(), $level) . " ? "
        . $this->processExpression($in->getThen(), $level) . ": "
        . $this->processExpression($in->getOperand(), $level);
    }
}