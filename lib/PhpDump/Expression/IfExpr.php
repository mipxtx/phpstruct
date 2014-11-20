<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class IfExpr extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\IfExpr $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $out = "if (" . $this->processExpression($in->getCondition(), $level) . ")";
        $out .= $this->processExpression($in->getThen(), $level);

        $else = $in->getElse();
        if ($else) {
            $out .= " else" . $this->processExpression($else, $level);
        }

        return $out;
    }
}