<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

Trait ParamsTrait
{

    /**
     * @var Base[]
     */
    private $params = [];

    public function addParam(Base $argument) {
        $this->params[] = $argument;

        return $this;
    }

    public function getParams() {
        return $this->params;
    }
}