<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

class ObjectCreate extends Base implements HasArgsInterface
{
    use ArgsTrait;

    private $name;

    function __construct($name) {
        $this->name = $name;
    }
}