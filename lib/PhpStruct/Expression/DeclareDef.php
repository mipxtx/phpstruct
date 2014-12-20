<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class DeclareDef extends Base implements HasScopes{

    private $arg;

    /**
     * @var Base
     */
    private $body;

    public function __construct(Base $arg){
        $this->arg = $arg;
    }

    /**
     * @return Base
     */
    public function getArg() {
        return $this->arg;
    }

    /**
     * @return Base
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param Base $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    public static function getConstructorFields(){
        return ["arg"];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}