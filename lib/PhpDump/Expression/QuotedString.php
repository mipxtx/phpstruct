<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;
use PhpStruct\Expression\Operator;

class QuotedString extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\QuotedString $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {
        $out = '"';
        foreach ($in->getElements() as $element) {
            $brace = ($element instanceof Variable || $element instanceof Operator);
            $out .= ($brace ? "{" : "") . $this->processExpression($element, $level) . ($brace ? "}" : "");
        }
        $out .= '"';

        return $out;
    }
}