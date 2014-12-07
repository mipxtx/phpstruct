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
     * @var IfSmall
     */
    private $if;

    /**
     * @var IfSmall[]
     */
    private $elseIfs = [];

    /**
     * @var Base
     */
    private $else;


    public static function getConstructorFields(){
        return ["if"];
    }

    function __construct(IfSmall $if) {
        $this->if = $if;
    }

    public function setElse(Base $else) {
        $this->else = $else;
    }

    public function getElse() {
        return $this->else;
    }
    public function addElseIf(IfSmall $elseif){
        $this->elseIfs[] = $elseif;
    }

    public function getIf(){
        return $this->if;
    }

    /**
     * @return IfSmall[]
     */
    public function getElseIfs(){
        return $this->elseIfs;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        $m = [$this->if];
        if($this->else){
            $m[] = $this->else;
        }

        return array_merge($m, $this->elseIfs);
    }
}