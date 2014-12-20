<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class FunctionCall extends Base implements HasParamsInterface
{
    use ParamsTrait;

    private $name;

    private $objectCreate = false;

    public function __construct(Base $name){
        $this->name = $name;
    }

    /**
     * @return Base
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return $this->getParams();
    }

    public static function getConstructorFields(){
        return ["name"];
    }

    /**
     * @return boolean
     */
    public function isObjectCreate() {
        return $this->objectCreate;
    }

    public function setObjectCreate() {
        $this->objectCreate = true;
    }
}