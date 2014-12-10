<?php
/**
 * @author: mix
 * @date: 15.11.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ForDef extends Base implements HasScopes
{

    private $define;

    private $condition;

    private $counter;

    /**
     * @var Scope
     */
    private $body;



    function __construct(Base $define, Base $condition, Base $counter, Scope $body) {
        $this->condition = $condition;
        $this->counter = $counter;
        $this->define = $define;
        $this->body = $body;
    }

    /**
     * @return Base
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return Base
     */
    public function getDefine() {
        return $this->define;
    }

    /**
     * @return Base
     */
    public function getCounter() {
        return $this->counter;
    }
    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }

    public static function getConstructorFields(){
        return ["define","condition","counter","body",];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}