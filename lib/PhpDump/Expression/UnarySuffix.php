<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class UnarySuffix extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\UnarySuffix $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $this->processExpression($in->getOperand(), $level) . $in->getOperator();
    }
}