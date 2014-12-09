<?php
/**
 * @author: mix
 * @date: 10.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;
use PhpStruct\Struct\ProcArgument;

class CatchDef extends Base implements HasScopes{

    private $arg;

    private $body;

    public function addArgDefine(ProcArgument $arg, Scope $body) {
        $this->arg = $arg;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getArg() {
        return $this->arg;
    }

    /**
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}