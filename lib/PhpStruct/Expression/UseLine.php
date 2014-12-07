<?php
/**
 * @author: mix
 * @date: 06.12.14
 */

namespace PhpStruct\Expression;

use PhpStruct\Base;

class UseLine extends Base
{

    private $class;

    private $as = null;

    private $mapping = null;

    public function __construct($class) {
        $this->class = $class;
    }

    /**
     * @param null $as
     */
    public function setAs($as) {
        $this->as = $as;
    }

    /**
     * @param null $mapping
     */
    public function setMapping($mapping) {
        $this->mapping = $mapping;
    }

    /**
     * @return mixed
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @return null
     */
    public function getAs() {
        return $this->as;
    }

    /**
     * @return null
     */
    public function getMapping() {
        return $this->mapping;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}