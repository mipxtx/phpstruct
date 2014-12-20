<?php
/**
 * @author: mix
 * @date: 08.12.14
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class UseLine extends BaseDump{
    /**
     * @param \PhpStruct\Expression\UseLine $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        $out =  $this->processExpression($in->getClass(),$level);
        if($in->getAs()){
            $out .= " as " . $this->processExpression($in->getAs(),$level);
        }

        return $out;

    }
}