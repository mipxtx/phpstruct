<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;
use PhpStruct\Expression\HasScopes;

class Scope extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\Scope $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $out = "";
        if ($level >= 0) {
            $out = " {\n";
        }

        foreach ($in->getScope() as $line) {
            $out .= $this->getLevelShift($level + 1) . $this->processExpression($line, $level + 1);
            if (!($line instanceof HasScopes)) {
                $out .= ";";
            }
            $out .= "\n";
        }

        if ($level >= 0) {
            $out .= $this->getLevelShift($level) . "}";
        }

        return $out;
    }
}