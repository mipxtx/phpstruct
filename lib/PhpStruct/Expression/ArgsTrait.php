<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

Trait ArgsTrait
{

    /**
     * @var Base[]
     */
    private $args = [];

    public function addArgument(Base $argument) {
        $this->args[] = $argument;

        return $this;
    }

    public function getArgs() {
        return $this->args;
    }
}