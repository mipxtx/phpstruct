<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class Dereference extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Dereference $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return "\${" . $this->processExpression($in->getBody(), $level) . "}";
    }
}