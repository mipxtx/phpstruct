<?php

spl_autoload_register(
    function ($class) {
        $filename = __DIR__ . "/lib/" . str_replace("\\","/",$class) . ".php";
        if(file_exists($filename)){
            require $filename;
        }
    }
);
