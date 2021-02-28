<?php

namespace App\Core\Utils;

class Request
{
    const REQUEST = 0;
    const REQUEST_POST = 1;
    const REQUEST_GET = 2;
    const REQUEST_COOKIE = 3;

    const COOKIE_EXPIRATION_MIN = 0;
    const COOKIE_EXPIRATION_HOUR = 1;
    const COOKIE_EXPIRATION_DAY = 2;


    public static function get(string $key)
    {
        return isset($_REQUEST[$key])
            ? (is_numeric($_REQUEST[$key]) ? (int) $_REQUEST[$key] : htmlspecialchars($_REQUEST[$key]))
            : null;
    }

    public static function getGET(string $key)
    {
        return isset($_GET[$key])
            ? (is_numeric($_GET[$key]) ? (int) $_GET[$key] : htmlspecialchars($_GET[$key]))
            : null;
    }

    public static function getPOST(string $key)
    {
        return isset($_POST[$key])
            ? (is_numeric($_POST[$key]) ? (int) $_POST[$key] : htmlspecialchars($_POST[$key]))
            : null;
    }

    public static function getCookie(string $key)
    {
        return isset($_COOKIE[$key])
            ? (is_numeric($_COOKIE[$key]) ? (int) $_COOKIE[$key] : htmlspecialchars($_COOKIE[$key]))
            : null;
    }

    public static function deleteCookie(string $key)
    {
        self::setCookie($key, '', [
            'expiration' => -1,
            'expiration_basis' => self::COOKIE_EXPIRATION_HOUR,
        ]);
    }

    public static function setCookie(string $key, string $value, array $options = [])
    {
        $path = '/';
        $expire = 0;

        if (!empty($options['expiration'])) {
            $expire = $options['expiration'];

            if (!empty($options['expiration_basis'])) {
                switch ($options['expiration_basis']) {
                    case self::COOKIE_EXPIRATION_DAY:
                        $expire *= 24 * 60 * 60;
                        break;
                    case self::COOKIE_EXPIRATION_HOUR:
                        $expire *= 60 * 60;
                        break;
                    case self::COOKIE_EXPIRATION_MIN:
                        $expire *=  60;
                        break;
                    default:
                        break;
                }
            }
            $expire += time();
        }

        setcookie($key, $value, $expire, $path);
    }

    public static function allGet(): array
    {
        return self::all(self::REQUEST_GET);
    }
    public static function allPost(): array
    {
        return self::all(self::REQUEST_POST);
    }
    public static function allRequest(): array
    {
        return self::all(self::REQUEST);
    }
    public static function allCookie(): array
    {
        return self::all(self::REQUEST_COOKIE);
    }

    private static function all(int $source = self::REQUEST): array
    {
        $res = $vars = [];
        
        switch ($source) {
            case self::REQUEST:
                $vars = $_REQUEST;
                break;
            case self::REQUEST_GET:
                $vars = $_GET;
                break;
            case self::REQUEST_POST:
                $vars = $_POST;
                break;
            case self::REQUEST_COOKIE:
                $vars = $_COOKIE;
                break;
        }

        
        foreach ($vars as $key => $value) {
            $res[$key] = is_numeric($value) ? (int) $value : htmlspecialchars($value);
        }

        return $res;
    }
}
