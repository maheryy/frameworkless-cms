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
     * Get all session variables
     */
    public static function getAll()
    {
        return $_SESSION;
    }

    /**
     * Remove a session variable
     * 
     * @param string $key
     */
    public static function delete(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy an active session
     */
    public static function stop()
    {
        if (self::isActive()) {
            session_destroy();
        }
    }

    /**
     * Start or resume a session
     */
    public static function start()
    {
        session_start();
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

    /**
     * Alias for isActive()
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Determines if the app is in developpement or in production
     *
     * @return bool
     */
    public static function isDev()
    {
        return defined('APP_DEV') && APP_DEV;
    }

    /**
     * Session has expired
     * Only in production
     *
     * @return bool
     */
    public static function hasExpired()
    {
        return  !self::isDev() && self::get('LAST_ACTIVE_TIME')
                && time() - self::get('LAST_ACTIVE_TIME') > SESSION_TIMEOUT * 60;
    }
}
