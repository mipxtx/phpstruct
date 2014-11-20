<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class EmptyStatement extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\EmptyStatement $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return '';
    }
}