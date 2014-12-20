<?php
/**
 * @author: mix
 * @date: 23.09.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ArrayDef extends Base implements HasParamsInterface
{
    use ParamsTrait;

    /**
     * @return Base[]
     */
    public function getChildren() {
        return $this->getParams();
    }

}