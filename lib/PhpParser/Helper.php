<?php
/**
 * @author: mix
 * @date: 08.11.14
 */

namespace PhpParser;

class Helper
{
    const STRING_LEN = 12;

    public static function buildTrace($trace) {
        $lines = [["FILE", "LINE"]];

        foreach ($trace as $codeline) {

            $file = isset($codeline["file"]) ? ($codeline["file"] . ":" . $codeline["line"]) : "internal function";

            $func = isset($codeline["class"]) ? ($codeline["class"] . $codeline["type"]) : "";
            $func .= $codeline["function"];
            $func .= "(";

            $args = [];
            foreach ($codeline["args"] as $argument) {
                if (is_object($argument)) {
                    $arg = get_class($argument);
                    if (method_exists($argument, "getId")) {
                        $arg .= "<" . $argument->getId() . ">";
                    }
                } elseif (is_array($argument)) {
                    $arg = "array[" . count($argument) . "]";
                } elseif (is_bool($argument)) {
                    $arg = $argument ? "TRUE" : "FALSE";
                } elseif (is_string($argument)) {

                    $argument = preg_replace("/\033\[[0-9;]+m/", "", $argument);
                    if (mb_strlen($argument) > self::STRING_LEN) {
                        $arg = str_replace("\n", "\\n", mb_strcut($argument, 0, self::STRING_LEN - 1)) . "…";
                    }
                } else {
                    $arg = $argument;
                }
                $args[] = $arg;
            }
            $func .= implode(",", $args);
            $func .= ")";

            $lines[] = [$file, $func];
        }

        $length = [];

        foreach ($lines as $row) {
            foreach ($row as $i => $item) {
                if (!isset($length[$i])) {
                    $length[$i] = 0;
                }
                $length[$i] = max($length[$i], mb_strlen($item));
            }
        }

        $out = "";

        foreach ($lines as $line) {
            foreach ($line as $i => $item) {
                $out .= $item;
                for ($j = mb_strlen($item); $j < $length[$i]; $j++) {
                    $out .= " ";
                }
                $out .= " ";
            }
            $out .= "\n";
        }

        return "\nTrace:\n" . $out;
    }

    public static function getTrace() {
        $trace = debug_backtrace(1);

        return self::buildTrace($trace);
    }

    public static function exception(\Exception $e) {
        echo "\nUncaught " . get_class($e) . " exception with messge: " .
            $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() .
            self::buildTrace($e->getTrace()) . "\n";
    }

    public function error($errno, $errstr, $errfile, $errline) {

        $errLevel = "Unknown[{$errno}]";

        switch ($errno) {
            case E_NOTICE :
                $errLevel = "Notice";
                break;
            case E_WARNING :
                $errLevel = "Warning";
                break;
            case E_RECOVERABLE_ERROR :
                $errLevel = "Catchable Fatal Error";
                break;
            case E_STRICT :
                $errLevel = "Strict Error";
                break;
        }

        $msg = "[$errLevel] $errstr at $errfile:$errline\n";

        $trace = debug_backtrace(1);
        array_shift($trace);
        $msg .= self::buildTrace($trace);
        echo $msg . "\n";

        if (in_array($errno, [E_RECOVERABLE_ERROR])) {
            exit();
        }
    }
} 