<?php
/**
 * @author: mix
 * @date: 15.11.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class QuotedString extends Base
{
    private $elements = [];

    public function addElement($element){
        $this->elements[] = $element;
    }

    public function getElements(){
        return $this->elements;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}