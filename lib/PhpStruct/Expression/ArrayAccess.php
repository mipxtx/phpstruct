<?php
/**
 * @author: mix
 * @date: 07.11.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ArrayAccess extends Base
{

    /**
     * @var Base
     */
    private $variable;

    /**
     * @var Base
     */
    private $access;

    public static function getConstructorFields(){
        return ["variable", "access"];
    }

    public function __construct(Base $var, Base $access) {
        $this->variable = $var;
        $this->access = $access;
    }

    public function getVariable() {
        return $this->variable;
    }

    public function getAccess() {
        return $this->access;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [$this->variable, $this->access];
    }
}