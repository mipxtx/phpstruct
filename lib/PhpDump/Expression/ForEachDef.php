<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ForEachDef extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ForEachDef $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return
            "foreach (" . $this->processExpression($in->getItem(), $level) . " as "
            . $this->processExpression($in->getEach(), $level) . ")"
            . $this->processExpression($in->getBody(), $level);
    }
}