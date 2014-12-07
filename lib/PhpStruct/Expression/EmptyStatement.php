<?php
/**
 * @author: mix
 * @date: 13.11.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class EmptyStatement extends Base
{
    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}