<?php
/**
 * @author: mix
 * @date: 30.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class IfSmall extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\IfSmall $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        return
            "if (" . $this->processExpression($in->getCondition(), $level) . ")"
            . $this->processExpression($in->getBody(), $level);
    }
}