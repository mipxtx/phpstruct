<?php
/**
 * @author: mix
 * @date: 17.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class Variable extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Variable $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return '$' . $in->getName();
    }
}