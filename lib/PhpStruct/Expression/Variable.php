<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;
use PhpStruct\HasNameInterface;

class Variable extends Base implements HasNameInterface
{
    use NamedTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}