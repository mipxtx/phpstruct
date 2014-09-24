<?php
/**
 * @author: mix
 * @date: 22.09.14
 */

namespace PhpStruct\Struct;


class ClassField {

    private $name;
    private $access;
    private $default;


    private $static = false;

    public function __construct($access,$name){
        $this->access = $access;
        $this->name = $name;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default) {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isStatic() {
        return $this->static;
    }

    /**
     *
     */
    public function setStatic() {
        $this->static = true;
    }


} 