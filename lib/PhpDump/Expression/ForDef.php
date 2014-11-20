<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ForDef extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ForDef $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return "for("
        . $this->processExpression($in->getDefine(), $level + 1) . "; "
        . $this->processExpression($in->getCondition(), $level + 1) . "; "
        . $this->processExpression($in->getCounter(), $level + 1) . ")"
        . $this->processExpression($in->getBody(), $level);
    }
}