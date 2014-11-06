<?php
/**
 * @author: mix
 * @date: 21.09.14
 */

namespace PhpStruct\Struct;

class Comment
{

    private $text;

    public function __construct($text) {
        $this->text = $text;
    }
} 