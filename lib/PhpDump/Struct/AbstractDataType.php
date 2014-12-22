<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;
use PhpStruct\Expression\HasScopes;

class AbstractDataType extends BaseDump
{
    /**
     * @param \PhpStruct\Struct\AbstractDataType $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {

        $out =
            $this->processModifiers($in) . $in->getType() . " " . $in->getName() . " ";

        if ($in->getExtends()) {
            $out .= "extends " . implode(", ", $in->getExtends()) . " ";
        }

        if ($in->getImplements()) {
            $out .= "implements " . implode(", ", $in->getImplements()) . " ";
        }

        $out .= "{\n";

        $members = array_merge($in->getUses(), $in->getFields(), $in->getMethods());

        foreach ($members as $member) {
            $out .=
                $this->getLevelShift($level+1)
                . $this->processHead($member, $level + 1)
                . $this->processModifiers($member)
                . $this->processExpression($member, $level + 1);
            if(! $member instanceof HasScopes){
                $out .= ";";
            }
            $out .= "\n";
        }

        $out .= $this->getLevelShift($level) . "}";

        return $out;
    }
}