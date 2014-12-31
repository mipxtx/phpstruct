<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpParser;

trait SupportTrait
{

    private $level = 0;

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

    public function end() {
        return $this->iterator->end();
    }

    public function getLogInfo(){
        $token = $this->current();

        $next = $token->next();
        $next2 = $token->next(2);

        return " $token | $next | $next2";

    }

    public function clog($message, $skipTokens = false) {
        if (!$this->debug) {
            return;
        }

        echo "$message:";

        if (!$skipTokens) {
            echo $this->getLogInfo();
        }
        echo "\n";
    }

    public function getLogMsg($msg) {
        foreach (["next" => "0;31", "start" => "0;32", "token" => "1;33", "arg " => "0;34"] as $key => $color) {
            $msg = str_replace($key, "\033[{$color}m{$key}\033[0m", $msg);
        }
        $msg = str_replace("\t", "    ", $msg);
        $shift = "";
        for ($i = 0; $i < $this->level; $i++) {
            $shift .= " ";
        }
        if (strpos($msg, "\n")) {
            $lines = explode("\n", $msg);
            $out = [];
            foreach ($lines as $i => $line) {
                $out[] = $shift . $line;
            }
            $msg = implode("\n", $out);
        } else {
            $msg = $shift . $msg;
        }

        return $msg;
    }

    public function log($msg, $skipTokens = false) {
        $this->cLog($this->getLogMsg($msg), $skipTokens);
    }

    /**
     * @param $msg
     * @param int $count
     * @return Token
     */
    public function logNext($msg, $count = 1) {
        $this->log("next $msg");

        return $this->next($count);
    }

    public function enableDebug() {
        $this->debug = true;
    }

    public function disableDebug() {
        $this->debug = false;
    }

    public function isEnabled() {
        return $this->debug;
    }
}