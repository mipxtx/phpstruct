<?php
/**
 * @author: mix
 * @date: 22.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\HasNameInterface;

class ClassField extends Base
{
    /**
     * @var HasNameInterface
     */
    private $name;

    /**
     * @var Base
     */
    private $default = null;

    public static function getConstructorFields(){
        return ["name"];
    }

    public function __construct(HasNameInterface $name) {
        $this->name = $name;
    }

    /**
     * @param Base $default
     */
    public function setDefault(Base $default = null) {
        $this->default = $default;
    }

    /**
     * @return Base
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @return Base
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [];
    }
}