<?php
/**
 * @author: mix
 * @date: 21.09.14
 */

namespace PhpStruct\Struct;


class AbstractDef {

    private $name;

    private $extends = [];


    private $implements = [];

    /**
     * @var ClassField[]
     */
    private $fields = [];

    /**
     * @var Method[]
     */
    private $methods = [];


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
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
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

    public function addMethod(Method $method){
        $this->methods[] = $method;
    }
} 