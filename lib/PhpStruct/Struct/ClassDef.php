<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

class ClassDef extends AbstractDef
{

    private $abstract = false;

    private $final = false;

    /**
     * @return bool
     */
    public function getAbstract() {
        return $this->abstract;
    }

    public function setAbstract() {
        $this->abstract = true;
    }

    /**
     * @return bool
     */
    public function getFinal() {
        return $this->final;
    }

    public function setFinal() {
        $this->final = true;
    }
}