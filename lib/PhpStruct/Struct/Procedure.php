<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\HasScopes;
use PhpStruct\Expression\Scope;

class Procedure extends Base implements HasScopes
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var ProcArgument[]
     */
    private $args = [];

    /**
     * @var Scope
     */
    private $body;

    public function __construct($name) {
        $this->name = $name;
    }

    public static function getConstructorFields(){
        return ["name"];
    }

    public function addArg(ProcArgument $arg) {
        $this->args[] = $arg;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return ProcArgument[]
     */
    public function getArgList() {
        return $this->args;
    }

    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param Code
     */
    public function setBody(Scope $body) {
        $this->body = $body;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [$this->body];
    }
}