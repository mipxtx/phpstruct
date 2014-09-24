<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

class ProcArgument
{

    private $name;

    private $type;

    private $default;

    public function __construct($name, $type = null) {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default) {
        $this->default = $default;
    }
}