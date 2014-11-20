<?php
/**
 * @author: mix
 * @date: 17.11.14
 */

namespace PhpDump;

use PhpStruct\Base;
use Symfony\Component\Process\Process;

abstract class BaseDump
{
    protected $in;

    public abstract function process($in, $level);



    /**
     * @param $level
     * @return string
     */
    public function getLevelShift($level) {
        $out = "";
        for ($i = 0; $i < $level; $i++) {
            $out .= "    ";
        }

        return $out;
    }

    /**
     * @param Base $scope
     * @param $level
     * @return string
     */
    public function processExpression(Base $scope, $level){
        return (new Processor())->process($scope,$level);
    }
}