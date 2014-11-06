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

        $line = $this->line !== null ? $this->line : ($this->getPreLine() ? "~" . $this->getPreLine() : null);

        return "[$this->number] "
        . var_export($this->getValue(), 1) . " (" . $this->getStringType() . ")"
        . ($line !== null ? " at line " . ($line) : "");
    }

    public function getPreLine() {
        $i = 0;
        $line = null;
        do {
            $i++;
            $pre = $this->prev($i);
            $line = $pre->getLine();
        } while (!$line);

        return $line;
    }

    public function getId() {
        return $this->number;
    }

    /**
     * @return bool
     */
    public function isDefinition() {
        switch ($this->getType()) {

            case T_ABSTRACT :
            case T_FINAL :
            case T_CLASS :
            case T_TRAIT :
            case T_INTERFACE :
            case T_FUNCTION:
                return true;
            default:
                return false;
        }
    }
} 