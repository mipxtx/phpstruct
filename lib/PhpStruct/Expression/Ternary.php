<?php
/**
 * @author: mix
 * @date: 13.11.14
 */

namespace PhpStruct\Expression;


class Ternary extends Operator{

    private $if;

    private $then;

    private $else;


    public function __construct(Base $if, Base $then = null){
        $this->if = $if;
        $this->then = $then;
    }

    public function setOperand(Base $operand) {
        $this->else = $operand;
    }

    /**
     * @return Base
     */
    public function getOperand() {
        return $this->else;
    }

    /**
     * @return Base
     */
    public function getIf() {
        return $this->if;
    }

    /**
     * @return Base
     */
    public function getThen() {
        return $this->then;
    }

}