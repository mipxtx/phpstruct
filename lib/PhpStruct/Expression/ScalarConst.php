<?php
/**
 * @author: mix
 * @date: 18.10.14
 */

namespace PhpStruct\Expression;

class ScalarConst extends Base
{

    use NamedTrait;

    /**
     * @var int
     */
    private $type;

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function dump(){
        return $this->nDump();
    }
} 