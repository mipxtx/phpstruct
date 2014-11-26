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

        $out = $this->processHead($in,$level) . $this->processModifiers($in) ."function " . $in->getName() . "(";
        $args = [];
        foreach ($in->getArgList() as $arg) {
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