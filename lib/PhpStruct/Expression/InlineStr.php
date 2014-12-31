<?php
/**
 * @author: mix
 * @date: 23.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class InlineStr extends Base{

    private $string;

    /**
     * @param string $string
     */
    public function __construct($string){
        $this->string = $string;
    }

    /**
     * @return mixed
     */
    public function getString() {
        return $this->string;
    }

    public static function getConstructorFields(){
        return ["string"];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}