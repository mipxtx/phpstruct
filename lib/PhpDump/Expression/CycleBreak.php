<?php
/**
 * @author: mix
 * @date: 17.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class CycleBreak extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\CycleBreak $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $in->getType();
    }
}