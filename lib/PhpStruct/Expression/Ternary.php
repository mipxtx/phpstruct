<?php
/**
 * @author: mix
 * @date: 13.11.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class Ternary extends Operator implements MultiOperand
{

    private $if;

    private $then;

    /**
     * else
     * @var
     */
    private $operand;

    public static function getConstructorFields(){
        return ["if", "then"];
    }

    public function __construct(Base $if, Base $then = null){
        $this->if = $if;
        $this->then = $then;
    }

    public function setOperand(Base $operand) {
        $this->operand = $operand;
    }

    /**
     * @return Base
     */
    public function getOperand() {
        return $this->operand;
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

    /**
     * @return Base[]
     */
    public function getChildren() {
        return[$this->if, $this->then, $this->operand];
    }
}