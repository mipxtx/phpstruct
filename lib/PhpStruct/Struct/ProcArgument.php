<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;

class ProcArgument extends Base
{

    private $name;

    /**
     * @var string
     */
    private $type = "";

    private $default = null;

    private $link = false;

    public static function getConstructorFields(){
        return ["name", "type"];
    }

    public function __construct($name, $type = "") {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function isLink() {
        return $this->link;
    }

    /**
     * 
     */
    public function setLink() {
        $this->link = true;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default) {
        $this->default = $default;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}