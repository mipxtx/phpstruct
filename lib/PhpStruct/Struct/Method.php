<?php
/**
 * @author: mix
 * @date: 23.09.14
 */

namespace PhpStruct\Struct;

class Method extends Procedure
{

    private $access;

    private $static = false;

    private $abstract = false;

    private $final = false;

    /**
     * @return string
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * @param string $visible
     */
    public function setAccess($visible) {
        $this->access = $visible;
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

    /**
     * @return boolean
     */
    public function isAbstract() {
        return $this->abstract;
    }

    /**
     * @param boolean $abstract
     */
    public function setAbstract() {
        $this->abstract = true;
    }

    /**
     * @return boolean
     */
    public function isFinal() {
        return $this->final;
    }

    /**
     * @param boolean $final
     */
    public function setFinal() {
        $this->final = true;
    }
}