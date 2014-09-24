<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Parser;

class Token
{

    private $type = 0;

    private $value;

    private $line = null;

    /**
     * @var TokenIterator
     */
    private $iterator;

    private $number;

    public function __construct($token, $number, TokenIterator $iterator) {
        $this->iterator = $iterator;
        $this->number = $number;
        if (is_array($token)) {
            $this->type = $token[0];
            $this->value = $token[1];
            $this->line = $token[2];
        } else {
            $this->value = $token;
        }
    }

    /**
     * @return int
     */
    public function getLine() {
        return $this->line;
    }

    public function getLastLine(){
        if($this->line){
            return $this->line;
        }else{
            return $this->iterator->getLastLine($this->getId());
        }
    }

    /**
     * @param $type
     * @return bool
     */
    public function isTypeOf($type) {
        return $type == $this->type;
    }

    public function getType() {
        return $this->type;
    }

    public function getStringType() {
        return token_name($this->type);
    }

    public function getValue() {
        return $this->value;
    }

    public function equal($eq) {
        return $this->value == $eq;
    }

    public function prev($count = 1) {
        return $this->iterator->getPrev($this->number, $count);
    }

    public function next($count = 1) {
        return $this->iterator->getNext($this->number, $count);
    }

    public function __toString() {
        return "[$this->number] " . var_export($this->getValue(), 1) . " (" . $this->getStringType() . ")" .
        ($this->line !== null ? " at line " . ($this->line) : "");
    }

    public function getId(){
        return $this->number;
    }
} 