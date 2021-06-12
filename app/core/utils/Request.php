<?php

namespace App\Core\Utils;

class Request
{
    const COOKIE_EXPIRATION_MIN = 0;
    const COOKIE_EXPIRATION_HOUR = 1;
    const COOKIE_EXPIRATION_DAY = 2;

    private array $dataPost;
    private array $dataGet;

    public function __construct()
    {
        $this->dataGet = $this->all($_GET);
        $this->dataPost = !empty($_POST) ? $this->all($_POST) : [];
    }

    public function __get($name)
    {
        if (!isset($_REQUEST[$name])) return null;

        return is_string($_REQUEST[$name])
            ? Formatter::sanitizeInput($_REQUEST[$name])
            : $_REQUEST[$name];
    }


    /**
     * Return the defined $_REQUEST[$key] variable, null otherwise
     * $_REQUEST contains $_GET, $_POST, $_COOKIE
     *
     * @param string $key
     * @return string|null
     */
    public function request(string $key)
    {
        return $this->getVariable($key, $_REQUEST);
    }

    /**
     * Return the defined $_REQUEST[$key] array variable, null otherwise
     *
     * @param string $key
     * @return array|null
     */
    public function requestArray(string $key)
    {
        return isset($_REQUEST[$key]) && is_array($_REQUEST[$key]) ? $_REQUEST[$key] : null;
    }

    /**
     * Return an url variable, null otherwise
     *
     * @param string $key
     * @return string|null
     */
    public function url(string $key)
    {
        return Formatter::decodeUrlQuery($_REQUEST[$key] ?? '');
    }

    /**
     * Return the defined $_GET[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key)
    {
        return $this->dataGet[$key] ?? null;
    }

    /**
     * Return the defined $_GET[$key] variable, null otherwise
     *
     * @param string $key
     * @return array|null

    public function getArray(string $key)
    {
        return isset($_GET[$key]) && is_array($_GET[$key]) ? $_GET[$key] : null;
    }
     */

    /**
     * Return the defined $_POST[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|int|array|null
     */
    public function post(string $key)
    {
        return $this->dataPost[$key] ?? null;
    }

    /**
     * Return the defined $_POST[$key] array variable, null otherwise
     *
     * @param string $key
     * @return array|null

    public function postArray(string $key)
    {
        return isset($_POST[$key]) && is_array($_POST[$key]) ? $_POST[$key] : null;
    }
     */

    /**
     * Return the defined $_DELETE[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|null
     */
    public function delete(string $key)
    {
        return $this->getVariable($key, $_REQUEST);
    }

    /**
     * Return the defined $COOKIE[$key] variable, null otherwise
     *
     * @param string $key
     * @return string|int|array|null
     */
    public function getCookie(string $key)
    {
        return $this->getVariable($key, $_COOKIE);
    }

    /**
     * Delete a cookie
     *
     * @param string $key
     * @return void
     */
    public function deleteCookie(string $key)
    {
        $this->setCookie($key, '', [
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
    public function setCookie(string $key, string $value, array $options = [])
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
     * Return a request header
     *
     * @param string $key
     * @return string|null
     */
    public function header(string $key)
    {
        return getallheaders()[$key] ?? null;
    }

    /**
     * Return all GET Variables
     * @return array
     */
    public function allGet()
    {
        return $this->dataGet;
    }

    /**
     * Return all POST Variables
     * @return array
     */
    public function allPost()
    {
        return $this->dataPost;
    }

    /**
     * Return all REQUEST Variables (GET, POST, COOKIE)
     * @return array
     */
    public function allRequest()
    {
        return $this->all($_REQUEST);
    }

    /**
     * Return all COOKIE Variables
     * @return array
     */
    public function allCookie()
    {
        return $this->all($_COOKIE);
    }

    /**
     * Return a COOKIE|POST|GET|REQUEST Variable
     *
     * @param string $key
     * @param int $source $_COOKIE|$_POST|$_GET|$_REQUEST
     * @return string|int|array|null
     */
    private function getVariable(string $key, array $source)
    {
        return isset($source[$key]) && is_string($source[$key])
            ? Formatter::sanitizeInput($source[$key])
            : null;
    }

    /**
     * Return all COOKIE|POST|GET|REQUEST Variables
     *
     * @param int $source $_COOKIE|$_POST|$_GET|$_REQUEST
     * @return array
     */
    public function all(array $source)
    {
        $res = [];
        foreach ($source as $key => $value) {
            $res[$key] = is_string($value) ? Formatter::sanitizeInput($value) : $value;
        }

        return $res;
    }

    /*
     * Determine if the request method is POST
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /*
     * Anonymous class to isolate post variables using request->post->variable_name
     *
    private function postClass() {
        return new class {
            public function __get($name)
            {
                if (!isset($_POST[$name])) return null;

                return is_string($_POST[$name])
                    ? Formatter::sanitizeInput($_POST[$name])
                    : $_POST[$name];
            }

            public function all() {
                $res = [];
                foreach ($_POST as $key => $value) {
                    $res[$key] = is_string($value) ? Formatter::sanitizeInput($value) : $value;
                }

                return $res;
            }
        };
    }
    */
}
