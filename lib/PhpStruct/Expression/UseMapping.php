<?php
/**
 * @author: mix
 * @date: 19.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class UseMapping extends Base{

    /**
     * @var Base
     */
    private $target;

    /**
     * @var Base
     */
    private $alias = null;

    public function __construct(Base $target){
        $this->target = $target;
    }

    /**
     * @return Base
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * @return Base
     */
    public function getAlias() {
        return $this->alias;
    }
    /**
     * @param Base $alias
     */
    public function setAlias(Base $alias) {
        $this->alias = $alias;
    }


    public static function getConstructorFields(){
        return ['target'];
    }
    /**
     * @return Base[]
     */
    public function getChildren() {
        // TODO: Implement getChildren() method.
    }
}