<?php
/**
 * @author: mix
 * @date: 10.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class DoDef extends Base{

    /**
     * @var Scope
     */
    private $body;

    /**
     * @var Base
     */
    private $condition;

    /**
     * @var string
     */
    private $type;

    function __construct(Scope $body, Base $condition, $type) {
        $this->body = $body;
        $this->condition = $condition;
        $this->type = $type;
    }

    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @return Base
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }



    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}