<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Expression;

use PhpParser\Helper;
use PhpStruct\Base;

class Scope extends Base
{

    /**
     * @var Base[]
     */
    private $expressions = [];

    /**
     * @param Base $expr
     */
    public function addExpression(Base $expr) {
        $this->expressions[] = $expr;
    }

    public function mergeScope(Scope $scope) {
        $this->expressions = array_merge($this->expressions, $scope->getScope());
    }

    /**
     * @return Base
     */
    public function first() {
        return $this->expressions[0];
    }

    public function count() {
        return count($this->expressions);
    }

    public function getScope() {
        return $this->expressions;
    }

    public function getLevelShift($level) {
        $out = "";
        for ($i = 0; $i < $level; $i++) {
            $out .= "    ";
        }

        return $level . $out;
    }
} 