<?php
/**
 * @author: mix
 * @date: 21.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\UseLine;

class AbstractDataType extends Base
{
    /**
     * class, interface, trait etc
     *
     * @var string
     */
    private $type;

    private $name;

    private $extends = [];

    private $implements = [];

    /**
     * @var ClassField[]
     */
    private $fields = [];

    /**
     * @var Procedure[]
     */
    private $methods = [];

    /**
     * @var UseLine[]
     */
    private $uses = [];

    /**
     * @var DefineUsage
     */
    private $constants = [];

    public static function getConstructorFields(){
        return ["type", "name"];
    }

    public function __construct($type, $name) {
        $this->type = $type;
        $this->name = $name;
    }

    public function getType() {
        return $this->type;
    }

    /**
     * @return ClassField[]
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @param ClassField $field
     */
    public function addField(ClassField $field) {
        $this->fields[] = $field;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getExtends() {
        return $this->extends;
    }

    /**
     * @param mixed $extends
     */
    public function addExtends($extends) {
        $this->extends[] = $extends;
    }

    /**
     * @return array
     */
    public function getImplements() {
        return $this->implements;
    }

    /**
     * @param array $implements
     */
    public function addImplements($implements) {
        $this->implements[] = $implements;
    }

    public function addMethod(Procedure $method) {
        $this->methods[] = $method;
    }

    public function getMethods() {
        return $this->methods;
    }

    /**
     * @return \PhpStruct\Expression\UseLine[]
     */
    public function getUses() {
        return $this->uses;
    }

    /**
     * @param \PhpStruct\Expression\UseLine $use
     */
    public function addUse($use) {
        $this->uses[] = $use;
    }


    /**
     * @return DefineUsage
     */
    public function getConstants() {
        return $this->constants;
    }

    /**
     * @param DefineUsage $constant
     */
    public function addConstant($constant) {
        $this->constants[] = $constant;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return $this->methods;
    }
}