<?php
/**
 * @author: mix
 * @date: 14.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;
use PhpStruct\HasNameInterface;

class DefineUsage extends Base implements HasNameInterface
{
    use NamedTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}