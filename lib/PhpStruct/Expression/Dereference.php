<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class Dereference extends Base{

    /**
     * @var Base
     */
    private $body;

    function __construct($body) {
        $this->body = $body;
    }

    /**
     * @return Base
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}