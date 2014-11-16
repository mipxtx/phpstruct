<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Expression;

class Base
{

    private $locked = false;

    private $headBlankLine;

    private $comment = "";

    public function hasScope() {
        return false;
    }

    public function lock() {
        $this->locked = true;
    }

    public function locked() {
        return $this->locked;
    }

    /**
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function hasHeadBlankLine() {
        return $this->headBlankLine;
    }

    /**
     *
     */
    public function setHeadBlankLine() {
        $this->headBlankLine = true;
    }

}