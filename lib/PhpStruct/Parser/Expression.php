<?php
/**
 * @author: mix
 * @date: 26.09.14
 */

namespace PhpStruct\Parser;

use PhpStruct\Expression\Base;
use PhpStruct\Expression\Scope;

class Expression
{
    use SupportTrait;



    public function __construct(TokenIterator $iterator) {
        $this->setIterator($iterator);
    }

    /**
     * @return Base;
     */
    public function processExpression() {

        $level = 0;

        $this->log("   expr start");
        do {
            $token = $this->current();
            $next = $token->next();
            if ($token->getValue() == "(" || $token->getValue() == "[") {
                $level++;
            }

            if ($token->getValue() == ")" || $token->getValue() == "]") {
                $level--;
            }
            $this->log("   level $level");
            //echo "level $level: $token\n";

            $done =
                ($level <= 0)
                && (
                    $next->getValue() == ";"
                    || $next->getValue() == ","
                    || $next->getValue() == ")"
                );

            $this->next();
        } while (!$done);


        return new Scope();
    }

}