<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class FunctionCall extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\FunctionCall $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $name = $in->getName();
        $args = [];
        foreach ($in->getArgs() as $arg) {
            $args[] = $this->processExpression($arg, $level + 1);
        }

        return "{$name}(" . implode(", ", $args) . ")";
    }
}