<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

Trait ArgsTrait
{

    /**
     * @var Base[]
     */
    private $args = [];

    public function addArg(Base $argument) {
        $this->args[] = $argument;

        return $this;
    }

    public function getArgs() {
        return $this->args;
    }
}