<?php
/**
 * @author: mix
 * @date: 07.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class CaseDef extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\CaseDef $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        return "case " . $this->processExpression($in->getCase(), $level) . " :\n"
        . $this->processExpression($in->getBody(), $level, ["noBrace" => 1]);
    }
}