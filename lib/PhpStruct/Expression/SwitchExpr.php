<?php
/**
 * @author: mix
 * @date: 06.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class SwitchExpr extends Base implements HasScopes{

    /**
     * @var Base
     */
    private $condition;
    /**
     * @var CaseDef[]
     */
    private $cases = [];

    /**
     * @var DefaultCase
     */
    private $default = null;

    function __construct(Base $condition) {
        $this->condition = $condition;
    }

    public function addCase(CaseDef $case){
        $this->cases[] = $case;
    }


    public function setDefault(DefaultCase $case){
        $this->default = $case;
    }

    /**
     * @return CaseDef[]
     */
    public function getCases() {
        return $this->cases;
    }

    /**
     * @return Base
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return DefaultCase
     */
    public function getDefault() {
        return $this->default;
    }


    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }



}