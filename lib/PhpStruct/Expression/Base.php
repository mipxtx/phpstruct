<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Expression;

class Base
{

    private $locked = false;

    public function hasScope() {
        return false;
    }

    public function lock() {
        $this->locked = true;
    }

    public function locked() {
        return $this->locked;
    }
}