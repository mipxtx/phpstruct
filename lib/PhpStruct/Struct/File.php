<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

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
     * @var Expresion
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
    }

    public function setNameSpace($name) {
        $this->namespace = $name;
    }

    public function setCode(Expresion $code) {
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
}