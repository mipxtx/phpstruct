<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Parser;

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

                $rawToken = $this->tokens[$tId];

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
        return new Token($this->tokens[$id], $id, $this);
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
}