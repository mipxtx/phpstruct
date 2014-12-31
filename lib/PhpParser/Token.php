<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpParser;

class Token
{
    // ?
    const T_TERNARY = 1000;
    // "
    const T_DOUBLE_QUOTE = 1001;
    // ,
    const T_COMMA = 1002;
    // :
    const T_COLON = 1003;
    // ;
    const T_SEMICOLON = 1004;
    // `
    const T_BACKTICK = 1005;
    // {
    const T_BRACE_OPEN = 1006;
    // }
    const T_BRACE_CLOSE = 1007;
    // [
    const T_SQ_BRACKETS_OPEN = 1008;
    // ]
    const T_SQ_BRACKETS_CLOSE = 1009;
    // (
    const T_BRACKETS_OPEN = 1010;
    // )
    const T_BRACKETS_CLOSE = 1011;
    // $
    const T_DOLLAR = 1012;


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
        '"' => self::T_DOUBLE_QUOTE,
        ':' => self::T_COLON,
        ';' => self::T_SEMICOLON,
        ',' => self::T_COMMA,
        '`' => self::T_BACKTICK,
        '{' => self::T_BRACE_OPEN,
        '}' => self::T_BRACE_CLOSE,
        '[' => self::T_SQ_BRACKETS_OPEN,
        ']' => self::T_SQ_BRACKETS_CLOSE,
        '(' => self::T_BRACKETS_OPEN,
        ')' => self::T_BRACKETS_CLOSE,
        '$' => self::T_DOLLAR,

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
     * @param $types
     * @return bool
     */
    public function isTypeOf($types) {
        if(!is_array($types)){
            $types = [$types];
        }
        $out = false;
        foreach($types as $type){
            $out |= ($type == $this->getType());
        }
        return $out;
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
        $value = str_replace("\n", '\n',trim($this->getValue()));
        if(mb_strlen($value) > 20){
            $value = mb_strcut($value,0,20) . "...";
        }
        return "[$this->number] '$value' (" . $this->getStringType() . ")" . ($line !== null ? " at line $line" : "");
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

    public function isBinary() {
        return in_array(
            strtolower($this->getValue()),
            [
                ".",
                ",",
                "+",
                "-",
                "*",
                "/",
                "%",
                "=",
                "&",
                "|",
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
                "<>",
                "<=",
                ">=",
                "+=",
                "-=",
                ".=",
                "|=",
                "&=",
                "*=",
                "/=",
                "%=",
                "^=",
                ">>=",
                "<<=",
                "<<",
                ">>",
                "^",
                "as",
                "instanceof",
                "and",
                "or",
                "xor",
            ]
        );
    }

    public function unarySuffix() {
        return in_array(
            $this->getValue(),
            ["++", "--",]
        );
    }

    public function isUnary() {
        return in_array(
            strtolower($this->getValue()),
            [
                "!",
                "@",
                "~",
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
                "(integer)",
                "(float)",
                "(double)",
                "(bool)",
                "(boolean)",
                "(string)",
                "(array)",
                "(object)",
                "&",
                "global",
                "throw",
                "clone",
                "$",
                "yield",
                "goto",
            ]
        );
    }

    public function canUnary(){
        return in_array( strtolower($this->getValue()), [ "-","+" ] );
    }

    public function getComment() {
        return $this->iterator->getComment($this->getId());
    }

    public function hasBlankLine() {
        return $this->iterator->hasBlankLine($this->getId());
    }

    public function isModifier(){
        return $this->isTypeOf([T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_CONST]);
    }
}