<?php
/**
 * @author: mix
 * @date: 23.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class ProcArgument extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\ProcArgument $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        return ($in->getType() ? $in->getType() . " " : "")
        . "$".$in->getName()
        . ($in->getDefault() ? (" = ". $this->processExpression($in->getDefault(),$level+1)) : "");
    }
}