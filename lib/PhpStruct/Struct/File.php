<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\Scope;

class File extends Base
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
     * @var Procedure[]
     */
    private $functions = [];

    /**
     * @var ClassDef[]
     */
    private $classes = [];

    /**
     * @var Scope
     */
    private $code;

    public function __construct($name) {
        $this->path = $name;
        $this->code = new Scope();
    }

    public function setNameSpace($name) {
        $this->namespace = $name;
    }

    public function addFunction(Procedure $function) {
        $this->functions[] = $function;
    }

    public function addCass(AbstractDataType $class) {
        $this->classes[] = $class;
    }

    public function addUse($full, $alias = null) {
        if ($alias === null) {
            $alias = array_pop(explode("\\", $full));
            $this->use[$alias] = $full;
        }
    }

    public function mergeScope(Scope $scope){
        $this->code->mergeScope($scope);
    }
    public  function addExpression(Base $expr){
        $this->code->addExpression($expr);
    }

    public function getFunctions(){
        return $this->functions;
    }

    public function getClasses(){
        return $this->classes;
    }

    public function getNamespace(){
        return $this->namespace;
    }

    public function getCode(){
        return $this->code;
    }

}