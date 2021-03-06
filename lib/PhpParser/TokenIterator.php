<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpParser;

class TokenIterator
{

    private $tokens = [];

    private $current = 0;

    private $currentLine = 0;

    private $debug = false;

    public function __construct(array $tokens) {
        $this->tokens = $tokens;
    }

    public function next($count = 1) {
        $this->current += $this->iterate($this->current, $count);

        return $this->current();
    }

    public function end() {
        $next = $this->current()->next();

        return $next->getId() >= count($this->tokens);
    }

    public function back() {
        $this->current += $this->iterate($this->current, -1);

        return $this->current();
    }

    public function getPrev($id = null, $count = 1) {
        if ($id === null) {
            $id = $this->current;
        }
        $id += $this->iterate($id, -$count);

        return $this->createTokenById($id);
    }

    public function getNext($id = null, $count = 1) {

        if ($id === null) {
            $id = $this->current;
        }
        $id += $this->iterate($id, $count);

        return $this->createTokenById($id);
    }

    public function current() {
        return $this->createTokenById($this->current);
    }

    public function iterate($start, $count = 1) {

        if ($this->debug) {
            echo "iterate $start / $count\n";
        }
        $id = 0;
        $absCount = abs($count);

        for ($i = 0; $i < $absCount; $i++) {
            do {
                if ($count > 0) {
                    $id++;
                } else {
                    $id--;
                }

                $tId = $start + $id;

                $rawToken = $this->getRawTocken($tId);

                if ($this->debug) {

                    echo "[$tId]";
                    echo var_export($rawToken, 1) . "\n";
                }

                if (isset($rawToken[2])) {
                    $this->currentLine = $rawToken[2];
                }
            } while (
                isset($rawToken[0])
                && (
                    $rawToken[0] == T_WHITESPACE
                    || $rawToken[0] == T_COMMENT
                    || $rawToken[0] == T_DOC_COMMENT
                )
            );
        }

        return $id;
    }

    public function createTokenById($id) {
        return new Token($this->getRawTocken($id), $id, $this);
    }

    public function getRawTocken($id) {
        $token = isset($this->tokens[$id]) ? $this->tokens[$id] : null;

        return $token;
    }

    public function getLine() {
        return $this->currentLine + 1;
    }

    public function getLastLine($id) {
        do {
            if (isset($this->tokens[$id][2])) {
                return $this->tokens[$id][2];
            }
        } while ($id == 0);

        return null;
    }

    public function startDebug() {
        $this->debug = true;
    }

    public function stopDebug() {
        $this->debug = false;
    }

    public function getComment($id){

        do{
            $id--;
            $token = $this->getRawTocken($id);
            if(!isset($token[0])){
                return "";
            }elseif(in_array($token[0],[T_DOC_COMMENT, T_COMMENT])){
                return $token[1];
            }elseif($token[0] == T_WHITESPACE || $this->isModifier($token)){
                continue;
            }else{
                return "";
            }
        }while(true);
    }

    public function hasBlankLine($id){
        do{
            $id--;
            $token = $this->getRawTocken($id);
            if(!isset($token[0])){
                return false;
            }elseif(in_array($token[0],[T_DOC_COMMENT, T_COMMENT]) || $this->isModifier($token)){
                continue;
            }elseif($token[0] == T_WHITESPACE){
                if(substr_count($token[1], "\n") > 1){
                    return true;
                }
                continue;
            }else{
                return false;
            }
        }while(true);
    }

    public function isModifier($token){
        return isset($token[0]) && in_array(
            $token[0],
            [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_VAR, T_STATIC]
        );
    }

}