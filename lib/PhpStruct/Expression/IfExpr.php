<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;


class IfExpr extends Scope{


    /**
     * @var Base
     */
    private $body;

    function __construct(Base $body) {
        $this->body = $body;
    }

    public function getBody(){
        return $this->body;
    }

    public function hasScope(){
        return true;
    }

} 