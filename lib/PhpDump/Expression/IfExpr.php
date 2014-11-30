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


        $out = $this->processExpression($in->getIf(),$level);


        foreach($in->getElseIfs() as $elseIf){
            $out .= " else" . $this->processExpression($elseIf, $level);
        }

        $else = $in->getElse();
        if ($else) {
            $out .= " else" . $this->processExpression($else, $level);
        }

        return $out;
    }
}