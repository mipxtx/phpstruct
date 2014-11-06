<?php
/**
 * @author: mix
 * @date: 18.10.14
 */

namespace PhpStruct\Expression;

class Binary extends Operator
{

    /**
     * @var Base
     */
    private $operand1;

    /**
     * @var Base
     */
    private $operand2;

    /**
     * @param $operator
     * @param Base $operand1
     */
    function __construct($operator, Base $operand1) {
        $this->setOperator($operator);
        $this->operand1 = $operand1;
    }

    /**
     * @param Base $operand
     */
    public function setOperand(Base $operand) {
        $this->operand2 = $operand;
    }

    /**
     * @return Base
     */
    public function getOperand() {
        return $this->operand2;
    }

    public function dump($level) {
        return "("
            . $this->operand1->dump($level)
            . $this->getOperator()
            . ($this->operand2 ? $this->operand2->dump($level) : "NULL")
            . ")";
    }
}