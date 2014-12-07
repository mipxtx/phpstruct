<?php
/**
 * @author: mix
 * @date: 30.11.14
 */

namespace PhpStruct;


class Modifiers {

    private $brackets = false;

    private $headBlankLine = false;

    private $comment = "";

    private $static = false;

    private $abstract = false;

    private $final = false;

    private $visibility = "";

    private $const = false;


    public function setBrackets() {
        $this->brackets = true;
    }

    public function hasBrackets() {
        return $this->brackets;
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


    /**
     * @return boolean
     */
    public function isAbstract() {
        return $this->abstract;
    }


    public function setAbstract() {
        $this->abstract = true;
    }

    /**
     * @return boolean
     */
    public function isFinal() {
        return $this->final;
    }

    /**
     *
     */
    public function setFinal() {
        $this->final = true;
    }

    /**
     * @return boolean
     */
    public function isStatic() {
        return $this->static;
    }

    /**
     *
     */
    public function setStatic() {
        $this->static = true;
    }

    /**
     * @return string
     */
    public function getVisibility() {
        return $this->visibility;
    }

    /**
     * @return boolean
     */
    public function isConst() {
        return $this->const;
    }

    /**
     * @param boolean $const
     */
    public function setConst() {
        $this->const = true;
    }


    /**
     * @param string $visibility
     */
    public function setVisibility($visibility) {
        $this->visibility = $visibility;
    }

    public static function __set_state($args){
        $obj = new static;
        foreach($args as $name => $value){
            $obj->$name = $value;
        }
        return $obj;
    }
} 