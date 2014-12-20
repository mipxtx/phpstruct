<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class DeclareDef extends BaseDump{



    /**
     * @param \PhpStruct\Expression\DeclareDef $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $body = $in->getBody();
        return "declare (" . $this->processExpression($in->getArg(),$level) . ")"
            . ($body?$this->processExpression($body,$level):";");
    }
}