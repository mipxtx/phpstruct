<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\UseLine;

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
    private $uses = [];

    /**
     * @return array
     */
    public function getUses() {
        return $this->uses;
    }

    /**
     * @var Procedure[]
     */
    private $functions = [];

    /**
     * @var AbstractDataType[]
     */
    private $classes = [];

    /**
     * @var Scope
     */
    private $code;


    public static function getConstructorFields(){
        return ["path"];
    }

    public function __construct($name) {
        $this->path = $name;
        $this->code = new Scope();
    }

    public function setNameSpaceItem(DefineUsage $name) {
        $this->namespace = $name->getName();
    }
    public function setNamespace($namespace){
        $this->namespace = $namespace;
    }

    public function addFunction(Procedure $function) {
        $this->functions[] = $function;
    }

    public function addClass(AbstractDataType $class) {
        $this->classes[] = $class;
    }

    public function addUse(UseLine $use) {
        $this->uses[] = $use;
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

    public function setCode(Scope $scope){
        $this->code = $scope;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return array_merge($this->classes, $this->functions, [$this->code]);
    }
}