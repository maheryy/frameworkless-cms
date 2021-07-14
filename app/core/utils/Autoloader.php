<?php

namespace App;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $class_name = ltrim(strrchr($class, '\\'), '\\');
            $path = preg_replace(
                ["/App\\\/i", "/\\\/", "/${class_name}$/i"],
                ['../', '/', "${class_name}.php"],
                strtolower($class)
            );
            if (file_exists($path)) {
                include $path;
            }
        });
    }
}
