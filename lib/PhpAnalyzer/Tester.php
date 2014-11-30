<?php
/**
 * @author: mix
 * @date: 29.11.14
 */

namespace PhpAnalyzer;


use PhpStruct\Struct\AbstractDataType;

class Tester {

    private $class;

    public function __construct(AbstractDataType $class){
        $this->class = $class;
    }


    public function createTests(){

    }
} 