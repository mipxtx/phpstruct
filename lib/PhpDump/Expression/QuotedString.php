<?php
/**
 * @author: mix
 * @date: 18.11.14
 */

namespace PhpDump\Expression;

use PhpDump\BaseDump;
use PhpStruct\Expression\ArrayAccess;
use PhpStruct\Expression\Operator;
use PhpStruct\Expression\Variable as myVar;

class QuotedString extends BaseDump
{
    /**
     * @param \PhpStruct\Expression\QuotedString $in
     * @param $level
     * @return string
     */
    public function process($in, $level) {

        $sign = $in->isExecute() ? "`" : '"';

        $out = $sign;
        foreach ($in->getElements() as $element) {
            $brace = ($element instanceof myVar || $element instanceof Operator || $element instanceof ArrayAccess);
            $out .= ($brace ? "{" : "") . $this->processExpression($element, $level) . ($brace ? "}" : "");
        }
        $out .= $sign;

        return $out;
    }
}