<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class IfExpr extends Base implements HasScopes
{

    /**
     * @var Base
     */
    private $condition;

    /**
     * @var Base
     */
    private $then;

    /**
     * @var Base
     */
    private $else;

    /**
     * @var Base[]
     */
    private $elseif = [];

    function __construct(Base $condition, Base $body) {
        $this->condition = $condition;
        $this->then = $body;
    }

    public function getCondition() {
        return $this->condition;
    }

    public function setElse(Base $else) {
        $this->else = $else;
    }

    public function getThen() {
        return $this->then;
    }

    public function getElse() {
        return $this->else;
    }
    public function addElseIf(Base $elseif, Base $body){
        $this->elseif[] = [$elseif, $body];
    }
}