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
    private $argList;

    /**
     * @var \PhpStruct\Base
     */
    private $body;

    public function __construct($name) {
        $this->name = $name;
    }

    public function addArg(ProcArgument $arg) {
        $this->argList[] = $arg;
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
        return $this->argList;
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