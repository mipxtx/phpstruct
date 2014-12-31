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
        $names = [];
        $empty = false;
        foreach($in->getNamespaces() as $ns){
            $name = $ns->getName();
            if(!$name){
                $empty = $ns;
            }else {
                $names[] = $ns;
            }

        }
        $enclosed = false;
        if($empty &&  $names){
            $enclosed = true;
        }

        if($empty){
            $out .= $this->processExpression($empty,$level,["enclosed" => $enclosed]) . "\n";
        }

        foreach($names as $name){
            $out .= $this->processExpression($name,$level,["enclosed" => $enclosed]) . "\n";
        }

        return $out;
    }
}