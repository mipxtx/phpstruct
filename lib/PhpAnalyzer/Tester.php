<?php
/**
 * @author: mix
 * @date: 29.11.14
 */

namespace PhpAnalyzer;

use PhpStruct\Base;
use PhpStruct\Expression\Binary;
use PhpStruct\Struct\AbstractDataType;
use PhpStruct\Struct\Procedure;

class Tester
{
    /**
     * @var AbstractDataType
     */
    private $class;

    private $testBranches = [];

    public function __construct(AbstractDataType $class) {
        $this->class = $class;
    }

    public function createTests() {
        $this->testBranches = [];
        $this->createTest($this->class->getMethods()[0]);
        print_r($this->testBranches);
    }

    public function createTest(Procedure $method){
        foreach ($this->getDeps($method->getBody()) as $req) {
            echo (new \PhpDump\Processor())->process($req, 0) . "\n";
        }

    }

    public function getDeps(Base $in) {
        if($in instanceof Binary &&  in_array($in->getOperator(), ["->", "::"])){
            return [$in];
        }else{
            $out = [];
            foreach($in->getChildren() as $child){
                $out = array_merge($out, $this->getDeps($child));
            }
        }
        return $out;
    }
} 