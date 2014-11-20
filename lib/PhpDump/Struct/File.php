<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class File extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\File $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        $out = "<?php\n";

        $out .= $this->processExpression($in->getCode(), -1);

        foreach ($in->getFunctions() as $func) {
            $out .= $this->processExpression($func, 0) . "\n";
        }

        foreach ($in->getClasses() as $class) {
            $out .= $this->processExpression($class, 0) . "\n";
        }

        return $out;
    }
}