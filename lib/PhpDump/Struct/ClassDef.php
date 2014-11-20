<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Struct;

use PhpDump\BaseDump;

class ClassDef extends BaseDump
{
    /**
     * @param $in \PhpStruct\Struct\ClassDef
     * @param $level
     * @return string
     */
    public function process($in, $level) {

        $out = ($in->isAbstract()? "abstract ":"") . ($in->isFinal()? "final ":"") . "class " . $in->getName() . " ";

        if($in->getExtends()){
            $out .= "extends " . $in->getExtends()[0] . " ";
        }

        if($in->getImplements()){
            $out .= "implements " . implode(", ",$in->getImplements()) . " ";

        }

        $out .= "{\n";

        foreach ($in->getFields() as $field) {
            $out .= $this->getLevelShift(1) . $field->getAccess() . " " .($field->isStatic()?"static ":"") . "$" .
                $field->getName() . ($field->getDefault()? " = " . $this->processExpression($field->getDefault(), 2):"") . ";\n\n";
        }

        foreach($in->getMethods() as $method){

        }


        $out .= "}";
        return $out;
    }


    public function gerKeywords(){

    }
}