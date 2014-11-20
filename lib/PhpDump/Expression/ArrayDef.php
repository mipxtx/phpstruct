<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class ArrayDef extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\ArrayDef $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $out = [];
        foreach ($in->getArgs() as $item) {
            $out[] = $this->processExpression($item, $level + 1);
        }

        return "[" . implode(", ", $out) . "]";
    }
}