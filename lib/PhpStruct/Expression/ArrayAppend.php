<?php
/**
 * @author: mix
 * @date: 12.11.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ArrayAppend extends Base
{

    private $variable;

    public function __construct(Base $var) {
        $this->variable = $var;
    }

    public function getVariable() {
        return $this->variable;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [$this->variable];
    }
}