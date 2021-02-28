<?php

namespace App\Core\Utils;

class Session
{

    public static function getUserId(): int
    {
        return (int) self::get('user_id');
    }

    public static function getRole(): int
    {
        return (int) self::get('user_role');
    }

    public static function isAdmin(): bool
    {
        return (bool) self::get('is_admin');
    }


    public static function load(array $data)
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }

    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public static function getAll(): array
    {
        return $_SESSION;
    }

    public static function delete(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function stop()
    {
        if (self::isActive()) {
            session_destroy();
        }
    }

    public static function start()
    {
        if (!self::isActive()) {
            session_start();
        }
    }

    public static function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
