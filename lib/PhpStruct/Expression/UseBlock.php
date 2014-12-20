<?php
/**
 * @author: mix
 * @date: 15.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class UseBlock extends Base implements HasScopes{
    private $uses = [];

    private $mappings = [];


    public function addUse(UseLine $line){
        $this->uses[] = $line;
    }

    public function getUses(){
        return $this->uses;
    }

    public function merge(UseBlock $block){
        $this->uses = array_merge($this->uses, $block->getUses());
    }

    public function addMapping(UseMapping $mapping){
         $this->mappings[] = $mapping;
    }

    /**
     * @return array
     */
    public function getMappings() {
        return $this->mappings;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}