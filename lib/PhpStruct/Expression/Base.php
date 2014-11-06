<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Expression;


class Base
{
    public function dump($level) {
        return " " . get_class($this);
    }

    public function locked() {
        return false;
    }

}