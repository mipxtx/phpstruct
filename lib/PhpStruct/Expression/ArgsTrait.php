<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

Trait ArgsTrait
{

    /**
     * @var Base[]
     */
    private $args = [];

    public function addArgument(Base $argument) {
        $this->args[] = $argument;
        return $this;
    }

    public function aDump(){
        $out = [];
        foreach($this->args as $arg){
            $out[] = $arg->dump(0);
        }
        return "(" .implode(",", $out) . ")";
    }
} 