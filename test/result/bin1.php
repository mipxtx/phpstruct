<?php
/**
 * @author: mix
 * @date: 28.10.14
 */
return new \PhpStruct\Expression\Binary(
    "=",
    new \PhpStruct\Expression\Variable('$a'),
    (new \PhpStruct\Expression\ScalarConst(2))->setType(T_LNUMBER)
);