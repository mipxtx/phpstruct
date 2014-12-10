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
        $out = "<?php\n\n";

        if($in->getNamespace()){
            $out .= "namespace " . $in->getNamespace() . ";\n\n";
        }

        foreach($in->getUses() as $use){
            $out .= $this->processExpression($use, 0) . ";\n";
        }

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