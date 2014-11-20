<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ArrayAppend extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ArrayAppend $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $this->processExpression($in->getVariable(), $level) . "[]";
    }
}