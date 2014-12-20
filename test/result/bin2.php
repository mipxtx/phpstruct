<?php
/**
 * @author: mix
 * @date: 28.10.14
 */
return new \PhpStruct\Expression\Binary(
  "->",
  new \PhpStruct\Expression\Variable('$ooo'),
  (new \PhpStruct\Expression\FunctionCall("callMe"))
      ->addParam((new \PhpStruct\Expression\ScalarConst(3))->setType(T_LNUMBER))
      ->addParam(new \PhpStruct\Expression\DefineUsage("__DIR__"))
      ->addParam(new \PhpStruct\Expression\Variable('$p'))
);