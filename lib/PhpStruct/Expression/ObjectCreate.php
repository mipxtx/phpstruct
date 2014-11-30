<?php
/**
 * @author: mix
 * @date: 22.10.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class ObjectCreate extends Base implements HasArgsInterface
{
    use ArgsTrait;

    private $name;


    public static function getConstructorFields(){
        return ["name"];
    }

    function __construct($name) {
        $this->name = $name;
    }
    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

}