<?php
/**
 * @author: mix
 * @date: 11.11.14
 */

namespace PhpStruct\Expression;

class ForEachDef extends Base implements HasScopes
{

    /**
     * @var Base
     */
    private $item;

    /**
     * @var Base
     */
    private $each;

    /**
     * @var Scope
     */
    private $body;

    public function __construct(Base $item, Base $each, Scope $body) {
        $this->item = $item;
        $this->each = $each;
        $this->body = $body;
    }

    /**
     * @return Scope
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @return Base
     */
    public function getEach() {
        return $this->each;
    }

    /**
     * @return Base
     */
    public function getItem() {
        return $this->item;
    }
}