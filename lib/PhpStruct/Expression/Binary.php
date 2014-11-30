<?php
/**
 * @author: mix
 * @date: 18.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class Binary extends Operator implements MultiOperand
{

    /**
     * @var Base
     */
    private $operand1;

    /**
     * @var Base
     */
    private $operand;

    /**
     * @param $operator
     * @param Base $operand1
     */
    function __construct($operator, Base $operand1) {
        $this->setOperator($operator);
        $this->operand1 = $operand1;
    }

    public static function getConstructorFields(){
        return ["operator", "operand1"];
    }

    /**
     * @param Base $operand
     */
    public function setOperand(Base $operand) {
        $this->operand = $operand;
    }

    /**
     * @return Base
     */
    public function getOperand() {
        return $this->operand;
    }

    public function getFirstOperand() {
        return $this->operand1;
    }
}