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


    /**
     * Return the defined $_REQUEST[$key] variable, null otherwise
     * $_REQUEST contains $_GET, $_POST, $_COOKIE
     * 
     * @param string $key
     * 
     * @return string|int|null 
     */
    public static function get(string $key)
    {
        return isset($_REQUEST[$key])
            ? (is_numeric($_REQUEST[$key]) ? (int) $_REQUEST[$key] : htmlspecialchars($_REQUEST[$key]))
            : null;
    }

    /**
     * Return the defined $_GET[$key] variable, null otherwise
     * 
     * @param string $key
     * 
     * @return string|int|null 
     */
    public static function getGET(string $key)
    {
        return isset($_GET[$key])
            ? (is_numeric($_GET[$key]) ? (int) $_GET[$key] : htmlspecialchars($_GET[$key]))
            : null;
    }

     /**
     * Return the defined $_POST[$key] variable, null otherwise
     * 
     * @param string $key
     * 
     * @return string|int|null 
     */
    public static function getPOST(string $key)
    {
        return isset($_POST[$key])
            ? (is_numeric($_POST[$key]) ? (int) $_POST[$key] : htmlspecialchars($_POST[$key]))
            : null;
    }

     /**
     * Return the defined $COOKIE[$key] variable, null otherwise
     * 
     * @param string $key
     * 
     * @return string|int|null 
     */
    public static function getCookie(string $key)
    {
        return isset($_COOKIE[$key])
            ? (is_numeric($_COOKIE[$key]) ? (int) $_COOKIE[$key] : htmlspecialchars($_COOKIE[$key]))
            : null;
    }

    /**
     * Delete a cookie
     * 
     * @param string $key
     * 
     * @return void
     */
    public static function deleteCookie(string $key)
    {
        self::setCookie($key, '', [
            'expiration' => -1,
            'expiration_basis' => self::COOKIE_EXPIRATION_HOUR,
        ]);
    }

    /**
     * Set a cookie
     * 
     * @param string $key
     * @param string $value
     * @param array $options 
     * Possible options :
     * - int expiration : 0 is default
     * - int expiration_basis : COOKIE_EXPIRATION_DAY|COOKIE_EXPIRATION_HOUR|COOKIE_EXPIRATION_MIN
     * - string path : '/' is default
     * 
     * @return void
     */
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

    /**
     * Return all GET Variables
     * 
     * @return array
     */
    public static function allGet()
    {
        return self::all(self::REQUEST_GET);
    }
    /**
     * Return all POST Variables
     * 
     * @return array
     */
    public static function allPost()
    {
        return self::all(self::REQUEST_POST);
    }
    /**
     * Return all REQUEST Variables (GET, POST, COOKIE)
     * 
     * @return array
     */
    public static function allRequest()
    {
        return self::all(self::REQUEST);
    }
    /**
     * Return all COOKIE Variables
     * 
     * @return array
     */
    public static function allCookie()
    {
        return self::all(self::REQUEST_COOKIE);
    }

    /**
     * Return all COOKIE|POST|GET|REQUEST Variables
     * 
     * @param int $source REQUEST|REQUEST_GET|REQUEST_POST|REQUEST_COOKIE
     * 
     * @return array
     */
    private static function all(int $source = self::REQUEST)
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
