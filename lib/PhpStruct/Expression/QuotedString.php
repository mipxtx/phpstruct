<?php
/**
 * @author: mix
 * @date: 15.11.14
 */

namespace PhpStruct\Expression;


class QuotedString extends Base{

    private $elements = [];


    public function addElement($element){
        $this->elements[] = $element;
    }

    public function getElements(){
        return $this->elements;
    }

} 