<?php
/**
 * @author: mix
 * @date: 31.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

interface OperatorInterface
{
    public function addOperand(Base $operand);
}