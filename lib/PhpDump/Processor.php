<?php
/**
 * @author: mix
 * @date: 17.11.14
 */

namespace PhpDump;

use PhpStruct\Base;

class Processor
{
    public function process(Base $code, $level) {

        $processor = $this->findProcessor($code);
        $out = "";


        $out .= $processor->process($code, $level);
        if ($code->hasBrackets()) {
            $out = "($out)";
        }

        return $out;
    }

    /**
     * @param Base $code
     * @throws FailException
     * @return BaseDump
     */
    public function findProcessor(Base $code) {
        $class = get_class($code);
        do {
            $name = str_replace("PhpStruct", "PhpDump", $class);
            try {
                return $this->createProcessor($name);
            } catch (FailException $e) {
                $class = get_parent_class($class);
            }
        } while ($class);

        throw new FailException("processor for " . get_class($code) . " not found");
    }

    /**
     * @factory
     * @param $name
     * @throws FailException
     * @return BaseDump
     */
    public function createProcessor($name) {
        if (class_exists($name)) {
            return new $name;
        }
        throw new FailException("cant create " . $name);
    }


}