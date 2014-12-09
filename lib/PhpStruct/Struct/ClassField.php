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

    private $name;

    private $default;

    public static function getConstructorFields(){
        return ["var"];
    }

    public function __construct( $name) {
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
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}