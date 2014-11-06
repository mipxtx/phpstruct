<?php
/**
 * @author: mix
 * @date: 23.10.14
 */

namespace PhpStruct\Expression;

class Unary extends Operator
{

    private $sign;

    /**
     * @var Base
     */
    private $expression;

    function __construct($sign) {

        $this->sign = $sign;
    }

    public function dump($level) {
        return $this->sign . "(" . ($this->expression ? $this->expression->dump($level) : "NULL") . ")";
    }

    public function setOperand(Base $operand) {
        $this->expression = $operand;
    }

    public function getOperand() {
        return $this->expression;
    }
}