<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\DefineUsage;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\UseBlock;
use PhpStruct\Expression\UseLine;

class File extends Base
{

    /**
     * @var string
     */
    private $path;

    /** @var NamespaceDef[] */
    private $namespaces = [];

    public static function getConstructorFields(){
        return ["path"];
    }

    public function __construct($name) {
        $this->path = $name;
    }

    public function addNamespace(NamespaceDef $ns){
        $this->namespaces[] = $ns;
    }

    /**
     * @return NamespaceDef[]
     */
    public function getNamespaces() {
        return $this->namespaces;
    }
    /**
     * @return Base[]
     */
    public function getChildren() {
        return $this->namespaces;
    }
}