<?php
/**
 * @author: mix
 * @date: 23.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class ClassField extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\ClassField $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        return
            $this->processHead($in, $level) . $this->processModifiers($in)
            . $this->processExpression($in->getVariable(), $level + 1)
            . ($in->getDefault() ? (" = " . $this->processExpression($in->getDefault(), $level + 1)) : "") . ";";
    }
}