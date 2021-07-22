<?php

namespace App\Core;

use App\Core\Utils\ConstantManager;

class Database
{
    private static \PDO $pdo_instance;

    const FETCH_ONE = 1;
    const FETCH_ALL = 2;

    const SAVE_DEFAULT = 0;
    const SAVE_IGNORE_NULL = 1;


    private function __construct()
    {
        try {
            $opts = $this->getPDOAttributes();
            self::$pdo_instance = self::connect(DB_HOST, DB_NAME, DB_USER, DB_PWD, 'mysql', $opts);
        } catch (\Exception $e) {
            throw new \Exception('SQL Error: ' . $e->getMessage());
        }
    }

    public static function connect(string $host, string $database, string $user, string $password, string $driver = 'mysql', array $opts = null)
    {
        $dsn = $driver . ':dbname=' . $database . ';host=' . $host;
        return new \PDO($dsn, $user, $password, $opts);
    }

    /**
     * Database singleton
     *
     * @return \PDO
     */
    public static function getInstance()
    {
        if (!isset(self::$pdo_instance)) {
            new Database();
        }

        return self::$pdo_instance;
    }

    /**
     * Get PDO Attributes
     *
     * @return array
     */
    private function getPDOAttributes()
    {
        return [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];
    }

    /**
     * Execute a prepared statement
     *
     * @param string $sql
     * @param array $params
     *
     * @return \PDOStatement
     */
    public static function execute(string $sql, array $params = null)
    {

        $st = self::getInstance()->prepare($sql);
        $st->execute($params);

        return $st;
    }

    /**
     * Checks .env file and tables in database
     *
     * @return bool
     */
    public static function isReady()
    {
        if (!file_exists(ConstantManager::$env_path)) return false;
        try {
            # Quick check if settings table exists
            self::execute('SELECT * FROM ' . DB_PREFIX . '_settings')->fetch();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Shorthand for getInstance()->inTransaction();
     */
    public static function inTransaction()
    {
        return self::getInstance()->inTransaction();
    }

    /**
     * Shorthand for getInstance()->beginTransaction();
     */
    public static function beginTransaction()
    {
        return !self::inTransaction() && self::getInstance()->beginTransaction();
    }

    /**
     * Shorthand for getInstance()->commit();
     */
    public static function commit()
    {
        return self::inTransaction() && self::getInstance()->commit();
    }

    /**
     * Shorthand for getInstance()->rollback();
     */
    public static function rollback()
    {
        return self::inTransaction() && self::getInstance()->rollback();
    }
}
