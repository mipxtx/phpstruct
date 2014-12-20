<?php
/**
 * @author: mix
 * @date: 11.12.14
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class ConditionLoop extends BaseDump{
    /**
     * @param \PhpStruct\Expression\ConditionLoop $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $condition = $in->getType() . " (" . $this->processExpression($in->getCondition(),$level) . ")";

        if($in->isConditionFirst()){
            $out = $condition . $this->processExpression($in->getBody(),$level);
        }else{
            $out = "do" . $this->processExpression($in->getBody(),$level) . " " .$condition . ";";
        }

        return $out;
    }
}