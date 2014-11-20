<?php
/**
 * @author: mix
 * @date: 23.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class Unary extends Operator
{

    /**
     * @var Base
     */
    private $expression;

    function __construct($sign) {
        $this->setOperator($sign);
    }

    public function setOperand(Base $operand) {
        $this->expression = $operand;
    }

    public function getOperand() {
        return $this->expression;
    }
}