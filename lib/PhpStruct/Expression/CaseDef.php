<?php
/**
 * @author: mix
 * @date: 07.12.14
 */

namespace PhpStruct\Expression;


use PhpStruct\Base;

class CaseDef extends DefaultCase{

    private $case;

    public function __construct(Scope $body, Base $case){
        $this->case = $case;
        parent::__construct($body);
    }

    /**
     * @return Base
     */
    public function getCase() {
        return $this->case;
    }

    public static function getConstructorFields(){
        return ["body", "case"];
    }

}