<?php
/**
 * @author: mix
 * @date: 20.09.14
 */

namespace PhpStruct\Struct;

use PhpStruct\Base;
use PhpStruct\Expression\HasParamsInterface;
use PhpStruct\Expression\HasScopes;
use PhpStruct\Expression\Scope;
use PhpStruct\Expression\Variable;

class Procedure extends Base implements HasScopes, HasParamsInterface
{

    /**
     * @var string
     */
    private $name = null;

    /**
     * @var ProcArgument[]
     */
    private $procArgs = [];

    /**
     * @var Scope
     */
    private $body;

    /**
     * @var Variable
     */
    private $uses = [];

    private $linkResult = false;

    /**
     * @return boolean
     */
    public function isLinkResult() {
        return $this->linkResult;
    }

    /**
     * @param boolean $linkResult
     */
    public function setLinkResult() {
        $this->linkResult = true;
    }

    /**
     * @return Variable
     */
    public function getUses() {
        return $this->uses;
    }

    /**
     * @param Variable $use
     */
    public function addUse(Base $use) {
        $this->uses[] = $use;
    }


    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    public function addProcArg(ProcArgument $arg) {
        $this->procArgs[] = $arg;
    }

    /**
     * @return ProcArgument[]
     */
    public function getProcArgs() {
        return $this->procArgs;
    }

    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param Code
     */
    public function setBody(Scope $body) {
        $this->body = $body;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        return [$this->body];
    }

    public function addParam(Base $argument) {
        $this->addUse($argument);
    }
}