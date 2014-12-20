<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class UseMapping extends BaseDump{
    /**
     * @param \PhpStruct\Expression\UseMapping $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $this->processExpression($in->getTarget(),$level)
            . " as " . $this->processModifiers($in->getAlias()) . $this->processExpression($in->getAlias(),$level);
    }
}