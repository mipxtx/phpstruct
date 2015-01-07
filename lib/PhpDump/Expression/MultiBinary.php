<?php
/**
 * @author: mix
 * @date: 08.01.15
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class MultiBinary extends BaseDump {
    /**
     * @param \PhpStruct\Expression\MultiBinary $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        $operator = $in->getOperator();

        $ops = [];
        foreach($in->getOperands() as $op){
            $ops[] = $this->processExpression($op,$level);
        }

        return implode(" $operator ", $ops);
    }
}