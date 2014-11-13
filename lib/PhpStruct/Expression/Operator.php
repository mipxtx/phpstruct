<?php
/**
 * @author: mix
 * @date: 02.11.14
 */

namespace PhpStruct\Expression;

abstract class Operator extends Base
{

    /**
     * @var string
     */
    private $operator;

    abstract public function setOperand(Base $operand);

    abstract public function getOperand();

    protected function setOperator($operator) {

        $this->operator = $operator;
    }

    public function getOperator() {
        return $this->operator;
    }
}