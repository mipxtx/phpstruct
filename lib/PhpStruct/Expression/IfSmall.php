<?php
/**
 * @author: mix
 * @date: 30.11.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class IfSmall extends Base
{

    private $condition;

    private $body;

    public static function getConstructorFields(){
        return ["condition", "body"];
    }

    public function __construct(Base $condition, Base $body){
        $this->condition = $condition;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [$this->condition, $this->body];
    }
}