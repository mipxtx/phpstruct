<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Expression\Base;
use PhpStruct\Expression\Scope;

class File
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $use = [];

    /**
     * @var Scope
     */
    private $code;

    /**
     * @var Procedure[]
     */
    private $functions = [];

    /**
     * @var ClassDef[]
     */
    private $classes = [];

    public function __construct($name) {
        $this->path = $name;
        $this->code = new Scope();
    }

    public function setNameSpace($name) {
        $this->namespace = $name;
    }

    public function setCode(Base $code) {
        $this->code = $code;
    }

    public function addFunction(Procedure $function) {
        $this->functions[] = $function;
    }

    public function addCass(AbstractDef $class) {
        $this->classes[] = $class;
    }

    public function __toString() {

        $pattern = '/:[a-zA-Z\\\\]+/';
        $str = preg_replace($pattern, "", print_r($this, 1));

        return $str;
    }

    public function addExp(Base $exp) {
        $this->code->addExpression($exp);
    }

    public function addUse($full, $alias = null) {
        if ($alias === null) {
            $alias = array_pop(explode("\\", $full));
            $this->use[$alias] = $full;
        }
    }

    public function getCode(){
        return $this->code;
    }
}