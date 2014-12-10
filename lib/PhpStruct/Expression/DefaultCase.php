<?php
/**
 * @author: mix
 * @date: 07.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class DefaultCase extends Base {

    /**
     * @var Scope
     */
    private $body;

    function __construct(Scope $body) {
        $this->body = $body;
    }


    public function getBody(){
        return $this->body;
    }


    public static function getConstructorFields(){
        return ["body",];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}