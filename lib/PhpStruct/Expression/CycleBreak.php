<?php
namespace PhpStruct\Expression;
/**
 * @author: mix
 * @date: 15.11.14
 */
class CycleBreak extends \PhpStruct\Expression\Base{

    /**
     * @var string
     */
    private $type;

    function __construct($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

}