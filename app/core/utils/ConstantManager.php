<?php

namespace App\Core\Utils;

use App\Core\Exceptions\NotFoundException;

class ConstantManager
{

    public static $env_path = '../config/.env';
    public static $conf_path = '../config/conf.inc.php';

    /**
     * Define a constant
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public static function defineConstant(string $key, string $value)
    {
        $key = str_replace(' ', '_', mb_strtoupper(trim($key)));
        if (!defined($key)) {
            define($key, trim($value));
        } else {
            throw new \Exception('la constante ' . $key . ' existe déjà');
        }
    }

    /**
     * Define constants located in .env and conf.inc.php
     *
     * @return void
     */
    public static function loadConstants()
    {
        if (file_exists('../config/debug.php')) {
            include '../config/debug.php';
        }

        # Constants in config/conf.inc.php
        include self::$conf_path;

        # Constants in .env.
        self::parseEnvFile(self::$env_path);
    }

    private static function parseEnvFile(string $path, bool $required = false)
    {
        if (!file_exists($path)) {
            if ($required) {
                throw new NotFoundException('Le fichier ' . $path . ' n\'existe pas');
            }
            return;
        }

        $env = fopen($path, 'r');
        if (!empty($env)) {
            while (!feof($env)) {
                $line = trim(fgets($env));
                $preg_results = [];
                if (preg_match('/([^=]*)=([^#]*)/', $line, $preg_results) && !empty($preg_results[1]) && !empty($preg_results[2])) {
                    self::defineConstant($preg_results[1], $preg_results[2]);
                }
            }
        }

    }

    public static function isConfigLoaded()
    {
        return file_exists(self::$env_path)
            && defined('DB_HOST')
            && defined('DB_NAME')
            && defined('DB_USER')
            && defined('DB_PWD')
            && defined('DB_PREFIX');
    }
}
