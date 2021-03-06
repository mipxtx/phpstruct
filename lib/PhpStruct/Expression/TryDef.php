<?php
/**
 * @author: mix
 * @date: 09.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;


class TryDef extends Base {

    /**
     * @var Scope
     */
    private $body;

    /**
     * @var CatchDef[]
     */
    private $catches = [];

    /**
     * @var Scope
     */
    private $finally = null;

    /**
     * @param Scope $body
     */
    public function __construct(Scope $body){
        $this->body = $body;
    }

    /**
     * @return Scope
     */
    public function getFinally() {
        return $this->finally;
    }

    /**
     * @param Scope $finally
     */
    public function setFinally(Scope $finally) {
        $this->finally = $finally;
    }

    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }

    public function addCatch(CatchDef $catch){
        $this->catches[] = $catch;
    }

    public function getCatches(){
        return $this->catches;
    }

    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }
    public static function getConstructorFields(){
        return ["body",];
    }

}