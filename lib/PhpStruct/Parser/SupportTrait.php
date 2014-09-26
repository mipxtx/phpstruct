<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Parser;

trait SupportTrait
{

    /**
     * @var TokenIterator;
     */
    private $iterator;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @return TokenIterator
     */
    public function getIterator() {
        return $this->iterator;
    }

    /**
     * @param TokenIterator $iterator
     */
    public function setIterator($iterator) {
        $this->iterator = $iterator;
    }

    public function current() {
        return $this->iterator->current();
    }

    public function next($count = 1) {
        return $this->iterator->next($count);
    }

    public function prev($count = 1) {
        return $this->iterator->getPrev($count);
    }

    public function end(){
        return $this->iterator->end();
    }


    public function log($message, $token = null) {
        if (!$this->debug) {
            return;
        }
        if (!$token) {
            $token = $this->current();
        }

        $next = $token->next();
        $next2 = $token->next(2);

        echo "$message: $token | $next | $next2\n";
    }

    public function enableDebug() {
        $this->debug = true;
    }

    public function disableDebug() {
        $this->debug = false;
    }
}