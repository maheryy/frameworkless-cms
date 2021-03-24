<?php

namespace App\Core;

class Database
{
    private static $pdo_instance = null;

    const FETCH_ONE = 1;
    const FETCH_ALL = 2;

    private function __construct()
    {
        try {
            $dsn = DB_DRIVER
                . ':dbname=' . DB_NAME
                . ';host=' . DB_HOST;

            self::$pdo_instance = new \PDO($dsn, DB_USER, DB_PWD);
            self::setPDOAttributes();
        } catch (\Exception $e) {
            throw new \Exception('SQL Error: ' . $e->getMessage());
        }
    }

    /**
     * Database singleton
     * 
     * @return PDO
     */
    public static function getInstance()
    {
        if (is_null(self::$pdo_instance)) {
            new Database();
        }

        return self::$pdo_instance;
    }

    /**
     * Set PDO Attributes
     * 
     * @return void
     */
    private static function setPDOAttributes()
    {
        $attributes = self::getPDOAttributes();
        foreach ($attributes as $key => $value) {
            self::$pdo_instance->setAttribute($key, $value);
        }
    }

    /**
     * Get PDO Attributes
     * 
     * @return array
     */
    private static function getPDOAttributes()
    {
        return [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
    }
}
