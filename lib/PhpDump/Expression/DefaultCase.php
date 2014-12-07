<?php
/**
 * @author: mix
 * @date: 07.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class DefaultCase extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\DefaultCase $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        return "default :\n" . $this->processExpression($in->getBody(), $level, ["noBrace" => 1]);
    }
}