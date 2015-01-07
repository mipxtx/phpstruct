<?php
/**
 * @author: mix
 * @date: 07.01.15
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class MultiBinary extends Base{

    private $operator;

    private $operands = [];

    public function __construct($operator){
        $this->operator = $operator;
    }

    public function addOperand(Base $operand){
        $this->operands[] = $operand;
    }

    public static function getConstructorFields(){
        return ["operator"];
    }

    /**
     * @return mixed
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * @return array
     */
    public function getOperands() {
        return $this->operands;
    }



    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}