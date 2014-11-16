<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpParser;

class Token
{
    const T_TERNARY = 1000;
    const T_QUOTE = 1001;

    private $type = 0;

    private $value;

    private $line = null;

    /**
     * @var TokenIterator
     */
    private $iterator;

    private $number;

    static $map = [
        "?" => self::T_TERNARY,
        '"' => self::T_QUOTE
    ];

    public function __construct($token, $number, TokenIterator $iterator) {
        $this->iterator = $iterator;
        $this->number = $number;
        if (is_array($token)) {
            $this->type = $token[0];
            $this->value = $token[1];
            $this->line = $token[2];
        } else {
            $this->value = $token;
            $this->type = isset(self::$map[$token]) ? self::$map[$token] : 0;
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

    /**
     * @param array|string $eq
     * @return bool
     */
    public function equal($eq) {
        if (!is_array($eq)) {
            $eq = [$eq];
        }
        $out = false;
        foreach ($eq as $item) {
            $out |= $this->value == $item;
        }

        return $out;
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
        return in_array(
            $this->getType(),
            [T_ABSTRACT, T_FINAL, T_CLASS, T_TRAIT, T_INTERFACE, T_FUNCTION]
        );
    }

    public function isBinary() {
        return in_array(
            $this->getValue(),
            [
                ".",
                ",",
                "+",
                "-",
                "*",
                "/",
                "%",
                "=",
                "->",
                "::",
                "=>",
                "&&",
                "||",
                "==",
                "!=",
                "!==",
                "===",
                ">",
                "<",
                "<=",
                "+=",
                "-=",
                ".=",
            ]
        );
    }

    public function unarySuffix() {
        return in_array(
            $this->getValue(),
            ["++", "--"]
        );
    }

    public function isUnary() {
        return in_array(
            strtolower($this->getValue()),
            [
                "!",
                "require",
                "require_once",
                "include",
                "include_once",
                "return",
                "echo",
                "print",
                "exit",
                "die",
                "(int)",
                "(float)"
            ]
        );
    }

    public function getComment(){
        return $this->iterator->getComment($this->getId());
    }

    public function hasBlankLine(){
        return $this->iterator->hasBlankLine($this->getId());
    }
}