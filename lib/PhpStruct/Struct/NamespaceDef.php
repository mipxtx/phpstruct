<?php
/**
 * @author: mix
 * @date: 21.12.14
 */

namespace PhpStruct\Struct;


use PhpStruct\Base;
use PhpStruct\Expression\HasScopes;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\UseBlock;

class NamespaceDef extends Base implements HasScopes{

    /**
     * @var string
     */
    private $name;

    /**
     * @var UseBlock
     */
    private $use;

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

    /**
     * @param $name
     */
    public function __construct($name){
        $this->name = $name;
        $this->code = new Scope();
        $this->use = new UseBlock();

    }

    /**
     * @return Scope
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return AbstractDataType[]
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * @return Procedure[]
     */
    public function getFunctions() {
        return $this->functions;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return UseBlock
     */
    public function getUse() {
        return $this->use;
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

    public function addUse(UseBlock $use) {
        $this->use->merge($use);
    }

    public function isEmpty(){
        return !($this->code->count() + count($this->classes) + count($this->functions));
    }

    public function addExpression(Base $expr){
        $this->code->addExpression($expr);
    }

    public function setCode(Scope $code){
        $this->code->mergeScope($code);
    }
    public function addLine(Base $line){
        if ($line instanceof AbstractDataType) {
            $this->addClass($line);
        } elseif ($line instanceof Procedure) {
            $this->addFunction($line);
        } elseif ($line instanceof UseBlock) {
            $this->addUse($line);
        } else {
            $this->addExpression($line);
        }
    }

    public static function getConstructorFields(){
        return ["name"];
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }

    public function setUse(UseBlock $use){
        $this->addUse($use);
    }

}