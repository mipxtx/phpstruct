<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpStruct\Expression;

class Variable extends Base
{
    use NamedTrait;

    public function dump($level){
        return $this->nDump();
    }
}