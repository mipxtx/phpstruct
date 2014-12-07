<?php
namespace PhpStruct\Expression;

use PhpStruct\Base;

/**
 * @author: mix
 * @date: 15.11.14
 */
class CycleBreak extends \PhpStruct\Base
{

    /**
     * @var string
     */
    private $type;

    public static function getConstructorFields(){
        return ["type"];
    }

    function __construct($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}