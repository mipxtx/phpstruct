<?php
/**
 * @author: mix
 * @date: 22.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class Procedure extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\Procedure $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {

        $out = "function " . ($in->isLinkResult()?"& ":""). $in->getName() . "(";
        $args = [];
        foreach ($in->getProcArgs() as $arg) {
            $args[] = $this->processExpression($arg,$level+1);
        }

        $out .= implode(", ", $args) . ")";

        if($in->getBody()){
            $out .= $this->processExpression($in->getBody(),$level);
        }else{
            $out .= ";";
        }

        $out .= "\n";

        return $out;
    }
} 