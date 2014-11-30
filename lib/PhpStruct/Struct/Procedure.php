<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;

class Procedure extends Base
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
     * @var \PhpStruct\Base
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
     * @return Base
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param Code
     */
    public function setBody($body) {
        $this->body = $body;
    }
}