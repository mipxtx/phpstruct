<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class DefineUsage extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\DefineUsage $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return $in->getName();
    }
}