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
    private $scope = [];

    /**
     * @param Base $expr
     */
    public function addExpression(Base $expr) {
        $this->scope[] = $expr;
    }

    public function mergeScope(Scope $scope) {
        $this->scope = array_merge($this->scope, $scope->getScope());
    }

    /**
     * @return Base
     */
    public function first() {
        return $this->scope[0];
    }

    public function count() {
        return count($this->scope);
    }

    public function getScope() {
        return $this->scope;
    }

    public function getLevelShift($level) {
        $out = "";
        for ($i = 0; $i < $level; $i++) {
            $out .= "    ";
        }

        return $level . $out;
    }
} 