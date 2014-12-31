<?php
/**
 * @author: mix
 * @date: 31.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class InlineStr extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\InlineStr $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return "echo '" . $in->getString() . "'";
    }
}