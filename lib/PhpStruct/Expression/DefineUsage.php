<?php
/**
 * @author: mix
 * @date: 14.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class DefineUsage extends Base
{
    use NamedTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}