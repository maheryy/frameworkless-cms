<?php

namespace App\Core\Utils;

class Request
{
    const COOKIE_EXPIRATION_MIN = 0;
    const COOKIE_EXPIRATION_HOUR = 1;
    const COOKIE_EXPIRATION_DAY = 2;


    /**
     * Return the defined $_REQUEST[$key] variable, null otherwise
     * $_REQUEST contains $_GET, $_POST, $_COOKIE
     *
     * @param string $key
     * @return string|null
     */
    public static function request(string $key)
    {
        return self::getVariable($key, $_REQUEST);
    }

    /**
     * Return the defined $_REQUEST[$key] array variable, null otherwise
     *
     * @param string $key
     * @return array|null
     */
    public static function requestArray(string $key)
    {
        return isset($_REQUEST[$key]) && is_array($_REQUEST[$key]) ? $_REQUEST[$key] : null;
    }

    /**
     * Return an url variable, null otherwise
     *
     * @param string $key
     * @return string|null
     */
    public static function url(string $key)
    {
        return $_REQUEST[$key] ?? null;
    }

    /**
     * Return the defined $_GET[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|null
     */
    public static function get(string $key)
    {
        return self::getVariable($key, $_GET);
    }

    /**
     * Return the defined $_GET[$key] variable, null otherwise
     *
     * @param string $key
     * @return array|null
     */
    public static function getArray(string $key)
    {
        return isset($_GET[$key]) && is_array($_GET[$key]) ? $_GET[$key] : null;
    }

    /**
     * Return the defined $_POST[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|int|array|null
     */
    public static function post(string $key)
    {
        return self::getVariable($key, $_POST);
    }

    /**
     * Return the defined $_POST[$key] array variable, null otherwise
     *
     * @param string $key
     * @return array|null
     */
    public static function postArray(string $key)
    {
        return isset($_POST[$key]) && is_array($_POST[$key]) ? $_POST[$key] : null;
    }

    /**
     * Return the defined $COOKIE[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|int|array|null
     */
    public static function getCookie(string $key)
    {
        return self::getVariable($key, $_COOKIE);
    }

    /**
     * Delete a cookie
     *
     * @param string $key
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
                        $expire *= 60;
                        break;
                    default:
                        break;
                }
            }
            $expire = time() + $expire;
        }

        setcookie($key, $value, $expire, $path);
    }

    /**
     * Return all GET Variables
     * @return array
     */
    public static function allGet()
    {
        return self::all($_GET);
    }

    /**
     * Return all POST Variables
     * @return array
     */
    public static function allPost()
    {
        return self::all($_POST);
    }

    /**
     * Return all REQUEST Variables (GET, POST, COOKIE)
     * @return array
     */
    public static function allRequest()
    {
        return self::all($_REQUEST);
    }

    /**
     * Return all COOKIE Variables
     * @return array
     */
    public static function allCookie()
    {
        return self::all($_COOKIE);
    }

    /**
     * Return a COOKIE|POST|GET|REQUEST Variable
     *
     * @param string $key
     * @param int $source $_COOKIE|$_POST|$_GET|$_REQUEST
     * @return string|int|array|null
     */
    private static function getVariable(string $key, array $source)
    {
        return isset($source[$key]) && is_string($source[$key])
            ? htmlspecialchars(trim($source[$key]))
            : null;
    }

    /**
     * Return all COOKIE|POST|GET|REQUEST Variables
     *
     * @param int $source $_COOKIE|$_POST|$_GET|$_REQUEST
     * @return array
     */
    private static function all(array $source)
    {
        $res = [];
        foreach ($source as $key => $value) {
            $res[$key] = is_string($value) ? htmlspecialchars(trim($value)) : $value;
        }

        return $res;
    }
}
