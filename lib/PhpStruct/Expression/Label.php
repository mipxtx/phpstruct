<?php
/**
 * @author: mix
 * @date: 31.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class Label extends Base{

    private $name;

    private function __construct($name){
        $this->name = $name;
    }

    public static function getConstructorFields(){
        return ["name"];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}