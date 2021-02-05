<?php

namespace App\Core;

class Database {
    public static $pdo_instance = null;
    const FETCH_ONE = 1;
    const FETCH_ALL = 2;
    
    private function __construct()
    {
        try {
            $dsn = DB_DRIVER 
                . ':dbname='. DB_NAME
                . ';host='. DB_HOST;
            
            self::$pdo_instance = new \PDO($dsn, DB_USER, DB_PWD);
            self::setPDOAttributes();
        } catch(\Exception $e) {
            throw new \Exception('SQL Error: ' . $e->getMessage());
        }
    }
    
    public static function getInstance() : \PDO
    {
        if ( is_null(self::$pdo_instance) ) {
           new Database();
        }

        return self::$pdo_instance;
    }

    private static function setPDOAttributes()
    {
        $attributes = self::getPDOAttributes();
        foreach($attributes as $key => $value) {
            self::$pdo_instance->setAttribute($key, $value);
        }
    }

    private static function getPDOAttributes() : array
    {
        return [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
    }
}