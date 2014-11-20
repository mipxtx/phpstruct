<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct;

class Base
{

    private $brackets = false;

    private $headBlankLine;

    private $comment = "";

    public function hasScope() {
        return false;
    }

    public function brackets() {
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



    private $static = false;

    private $abstract = false;

    private $final = false;

    private $visibility = "public";

    /**
     * @return boolean
     */
    public function isAbstract() {
        return $this->abstract;
    }

    /**
     * @param boolean $abstract
     */
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
     * @param boolean $final
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
     * @param boolean $static
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
     * @param string $visibility
     */
    public function setVisibility($visibility) {
        $this->visibility = $visibility;
    }

    public function copyModifiers(Base $from){
        $this->visibility = $from->getVisibility();
        $this->abstract = $from->isAbstract();
        $this->static = $from->isAbstract();
        $this->final = $from->isFinal();
    }

}