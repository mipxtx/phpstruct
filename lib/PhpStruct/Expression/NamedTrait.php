<?php
/**
 * @author: mix
 * @date: 26.10.14
 */

namespace PhpStruct\Expression;


trait NamedTrait {
    private $name;

    function __construct($name) {
        $this->name = $name;
    }

    public function nDump(){
        return $this->name;
    }

    public function getName(){
        return $this->name;
    }
} 