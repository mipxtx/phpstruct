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
     * @param \PhpStruct\Struct\AbstractDataType $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {

        $out =
            $this->processHead($in, $level) . $this->processModifiers($in) . $in->getType() . " " . $in->getName()
            . " ";

        if ($in->getExtends()) {
            $out .= "extends " . implode(", ", $in->getExtends()) . " ";
        }

        if ($in->getImplements()) {
            $out .= "implements " . implode(", ", $in->getImplements()) . " ";
        }

        $out .= "{\n";

        $members = array_merge($in->getUses(),$in->getFields(),$in->getMethods());

        foreach($members as $member){
            $out .= $this->getLevelShift($level + 1) . $this->processExpression($member, $level + 1) . ";\n";
        }

        $out .= "}";

        return $out;
    }
}