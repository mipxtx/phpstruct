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
    public function process($in, $level, $params = []) {
        $out = "";
        $noBrace = isset($params["noBrace"]);
        if ($level >= 0 && !$noBrace) {
            $out = " {\n";
        }
        $lines = [];
        foreach ($in->getScope() as $line) {
            $codeLine = $this->getLevelShift($level+1)
                . $this->processHead($line, $level + 1)
                . $this->processModifiers($line)
                . $this->processExpression($line, $level + 1);
            if (!($line instanceof HasScopes)) {
                $codeLine .= ";";
            }
            $lines[] = $codeLine;
        }
        $out .= implode("\n", $lines);

        if ($level >= 0 && !$noBrace) {
            $out .= "\n".$this->getLevelShift($level) . "}";
        }

        return $out;
    }
}