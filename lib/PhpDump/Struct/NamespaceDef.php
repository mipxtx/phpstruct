<?php
/**
 * @author: mix
 * @date: 22.12.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class NamespaceDef extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\NamespaceDef $in
     * @param int $level
     * @return string
     */
    public function process($in, $level, $params = []) {
        $level = 0;
        $out = "";
        $name = $in->getName();
        $enclosed = $params["enclosed"];

        if ($name || $enclosed) {
            $out .= "namespace $name";
        }

        if ($name && !$enclosed) {
            $out .= ";\n";
        }

        if ($enclosed) {
            $out .= " {\n";
            $level = 1;
        }

        $use = $in->getUse();

        if (count($use->getUses())) {
            $out .= $this->getLevelShift($level) . $this->processExpression($use, $level) . "\n";
        }

        $out .= $this->processExpression($in->getCode(), $level - 1, ["noBrace" => 1]) . "\n";

        foreach ($in->getFunctions() as $function) {
            $out .= $this->getLevelShift($level) . $this->processHead($function, $level) .
                $this->processExpression($function, $level);
        }

        foreach ($in->getClasses() as $class) {
            $out .= $this->getLevelShift($level) . $this->processHead($class, $level) .
                $this->processExpression($class, $level);
        }

        if ($enclosed) {
            $out .= "\n}";
        }

        return $out;
    }
}