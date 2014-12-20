<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

interface HasParamsInterface
{
    public function addParam(Base $argument);
}