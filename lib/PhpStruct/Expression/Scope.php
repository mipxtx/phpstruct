<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Expression;


class Scope extends Base{

    /**
     * @var Base[]
     */
    private $scope = [];

    public function addExpression(Base $expr){
        $this->scope[] = $expr;
    }
} 