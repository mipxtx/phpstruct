<?php

/**
 * Class A
 */
abstract class A extends O implements BB, CC
{

    /**
     * @var null
     */
    private static $a = null;

    /**
     * @return mixed
     */
    abstract function aaa();

    /**
     * @param $b
     * @return int
     */
    public function bbb($b) {
        return $b ^ 2;
    }

    /**
     * @param O $a
     * @param int $b
     * @return int
     */
    public static function ccc(O $a = null, $b=3) {
        return $a + $b + self::$a;
    }
}

interface BB
{
    public function bbb($b);
}

interface CC
{
    public static function ccc(O $a = null, $b = 3);
}

/**
 * Class O
 */
class O {
    public function oo(){}
}
