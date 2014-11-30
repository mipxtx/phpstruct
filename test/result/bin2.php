<?php
/**
 * @author: mix
 * @date: 28.10.14
 */
return new \PhpStruct\Expression\Binary(
  "->",
  new \PhpStruct\Expression\Variable('$ooo'),
  (new \PhpStruct\Expression\FunctionCall("callMe"))
      ->addArg((new \PhpStruct\Expression\ScalarConst(3))->setType(T_LNUMBER))
      ->addArg(new \PhpStruct\Expression\DefineUsage("__DIR__"))
      ->addArg(new \PhpStruct\Expression\Variable('$p'))
);