<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;

class UseBlock extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\UseBlock $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {

        $uses = [];

        foreach ($in->getUses() as $use) {
            $uses[] = $this->processExpression($use, $level + 1);
        }

        $mappings = [];

        foreach ($in->getMappings() as $mapping) {
            $mappings[] = $this->getLevelShift($level + 1) . $this->processExpression($mapping, $level + 1);
        }

        return "use " . implode(", ", $uses)
        . (count($mappings) ? " {\n" . implode("\n", $mappings) . "\n" . $this->getLevelShift($level) . "}" : ";");
    }
}