<?php
/**
 * @author: mix
 * @date: 18.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ScalarConst extends Base
{
    use NamedTrait;

    /**
     * @var int
     */
    private $type;

    public function setType($type) {
        $this->type = $type;

        return $this;
    }
}