<?php

namespace App\Core\Utils;

class Session
{

    /**
     * @return int
     */
    public static function getUserId()
    {
        return (int) self::get('user_id');
    }

    /**
     * @return int
     */
    public static function getRole()
    {
        return (int) self::get('user_role');
    }

    /**
     * @return bool
     */
    public static function isAdmin()
    {
        return (bool) self::get('is_admin');
    }


    /**
     * Set multiple session variables
     * 
     * @param array $data
     * 
     * @return void
     */
    public static function load(array $data)
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * @param string $key
     * @param int|string $value
     * 
     * @return void
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * 
     * @return string|int|bool
     */
    public static function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    /**
     * @return void
     */
    public static function getAll()
    {
        return $_SESSION;
    }

    /**
     * Remove a session variable
     * 
     * @param string $key
     * 
     * @return void
     */
    public static function delete(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy an active session
     * 
     * @return void
     */
    public static function stop()
    {
        if (self::isActive()) {
            session_destroy();
        }
    }

    /**
     * Start or resume a session
     * 
     * @return void
     */
    public static function start()
    {
        if (!self::isActive()) {
            session_start();
        }
    }

    /**
     * Session is currently active
     * 
     * @return bool
     */
    public static function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
