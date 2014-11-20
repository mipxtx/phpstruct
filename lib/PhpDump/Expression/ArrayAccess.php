<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ArrayAccess extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ArrayAccess $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $var = $this->processExpression($in->getVariable(), $level);
        $acc = $this->processExpression($in->getAccess(), $level);

        return "{$var}[{$acc}]";
    }
}