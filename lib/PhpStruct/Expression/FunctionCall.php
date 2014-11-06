<?php
/**
 * @author: mix
 * @date: 21.10.14
 */

namespace PhpStruct\Expression;

class FunctionCall extends Base implements HasArgsInterface
{
    use ArgsTrait, NamedTrait;

    public function dump($level){
        return $this->nDump() . $this->aDump();
    }
}