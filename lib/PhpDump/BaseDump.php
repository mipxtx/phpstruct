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
     * @param int $level
     * @param array $params
     * @return string
     */
    public function processExpression(Base $scope, $level, array $params = []) {
        return (new Processor())->process($scope, $level, $params);
    }

    public function processModifiers(Base $code) {
        $out = $code->isAbstract() ? "abstract " : "";
        $out .= $code->getVisibility() ? $code->getVisibility() . " " : "";
        $out .= $code->isStatic() ? "static " : "";
        $out .= $code->isFinal() ? "final " : "";

        return $out;
    }

    public function processHead(Base $code, $level) {
        $out = "";
        if ($code->hasHeadBlankLine()) {
            $out .= "\n" . $this->getLevelShift($level);
        }
        if ($code->getComment()) {
            $out .= $code->getComment() . "\n" . $this->getLevelShift($level);
        }

        return $out;
    }
}