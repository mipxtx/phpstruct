<?php
/**
 * @author: mix
 * @date: 22.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\Variable;

class ClassField extends Base
{

    private $var;

    private $default;

    public static function getConstructorFields(){
        return ["var"];
    }

    public function __construct(Variable $variable) {
        $this->var = $variable;
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
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getVariable() {
        return $this->var;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}