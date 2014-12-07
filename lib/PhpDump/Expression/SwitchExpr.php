<?php
/**
 * @author: mix
 * @date: 07.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class SwitchExpr extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\SwitchExpr $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        $out = "switch(" . $this->processExpression($in->getCondition(), $level) . ") {\n";

        foreach ($in->getCases() as $case) {
            $out .= $this->getLevelShift($level + 1)
                . $this->processExpression($case, $level + 1) . "\n";
        }
        $out .= $this->getLevelShift($level + 1)
            . $this->processExpression($in->getDefault(), $level + 1) . "\n";
        $out .= $this->getLevelShift($level) . "}";

        return $out;
    }
}