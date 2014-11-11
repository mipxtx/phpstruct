<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Expression\Base;
use PhpStruct\Expression\Scope;

class File extends Scope
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $use = [];

    /**
     * @var Procedure[]
     */
    private $functions = [];

    /**
     * @var ClassDef[]
     */
    private $classes = [];

    public function __construct($name) {
        $this->path = $name;
        $this->code = new Scope();
    }

    public function setNameSpace($name) {
        $this->namespace = $name;
    }

    public function addFunction(Procedure $function) {
        $this->functions[] = $function;
    }

    public function addCass(AbstractDef $class) {
        $this->classes[] = $class;
    }

    public function addUse($full, $alias = null) {
        if ($alias === null) {
            $alias = array_pop(explode("\\", $full));
            $this->use[$alias] = $full;
        }
    }
}