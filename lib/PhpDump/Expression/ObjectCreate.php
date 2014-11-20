<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ObjectCreate extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ObjectCreate $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        $out = [];

        foreach ($in->getArgs() as $arg) {
            $out[] = $this->processExpression($arg, $level);
        }

        return "new " . $in->getName() . "(" . implode(", ", $out) . ")";
    }
}