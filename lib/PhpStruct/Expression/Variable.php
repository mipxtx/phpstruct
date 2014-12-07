<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class Variable extends Base
{
    use NamedTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}