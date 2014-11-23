<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class AbstractDataType extends BaseDump
{
    /**
     * @param $in \PhpStruct\Struct\AbstractDataType
     * @param $level
     * @return string
     */
    public function process($in, $level) {



        $out = $this->processHead($in,$level) . $this->processModifiers($in) . $in->getType() . " " . $in->getName() . " ";

        if ($in->getExtends()) {
            $out .= "extends " . implode(", ", $in->getExtends()) . " ";
        }

        if ($in->getImplements()) {
            $out .= "implements " . implode(", ", $in->getImplements()) . " ";
        }

        $out .= "{\n";

        foreach ($in->getFields() as $field) {

            $out .= $this->getLevelShift($level+1) . $this->processExpression($field, $level+1) . "\n";
        }

        foreach ($in->getMethods() as $method) {




            $out .= $this->getLevelShift($level+1) . $this->processExpression($method, $level+1) . "\n";
        }

        $out .= "}";

        return $out;
    }
}