<?php

spl_autoload_register(function($class) {
    $parse = explode('\\', $class);
    foreach ($parse as $key => $val) {
        $parse[$key] = ucfirst($val);
    }
    $file_path = PATH . '/' . ltrim(implode('/', $parse), '/') . '.php';
    include_once $file_path;
});

