<?php
/**
 * @author: mix
 * @date: 23.09.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ArrayDef extends Base implements HasArgsInterface
{
    use ArgsTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return $this->getArgs();
    }

}