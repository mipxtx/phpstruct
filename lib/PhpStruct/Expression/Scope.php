<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Expression;

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
        if(!$this->scope && $expr instanceof Scope){
            $this->scope = $expr->getScope();
        }
        $this->scope[] = $expr;
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


    public function dump($level){
        $level++;
        $out = [];

        foreach ($this->scope as $expr) {
            $out[] = $expr->dump($level) . ";";
        }
        return implode("\n", $out);
    }

    public function getScope(){
        return $this->scope;
    }
} 