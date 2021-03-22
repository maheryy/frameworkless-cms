<?php

namespace App;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $class = ucwords(str_ireplace(
                ['App\\', '\\'],
                ['', '/'],
                $class
            ));

            if (file_exists($class . '.php')) {
                include $class . '.php';
            }
        });
    }
}
